<?php
namespace Tests\ObjectivePHP\Events;

use ObjectivePHP\Events\Callback\AbstractCallback;
use ObjectivePHP\Events\Callback\AliasedCallback;
use ObjectivePHP\Events\Callback\CallbacksAggregate;
use ObjectivePHP\Events\Event;
use ObjectivePHP\Events\EventsHandler;
use ObjectivePHP\Events\Exception;
use ObjectivePHP\Matcher\Matcher;
use ObjectivePHP\PHPUnit\TestCase;
use ObjectivePHP\ServicesFactory\Reference;
use ObjectivePHP\ServicesFactory\ServiceReference;
use ObjectivePHP\ServicesFactory\ServicesFactory;
use ObjectivePHP\ServicesFactory\Specs\ClassServiceSpecs;

class Events extends TestCase
{

    public function testCanBindEvents()
    {

        $eventsHandler = new EventsHandler();

        $callback = function ()
        {
        };

        $eventsHandler->bind('event.name', $callback);

        // inject a matcher to prevent using default one, and test matcher accessors
        $eventsHandler->setMatcher($matcher = new Matcher());
        $this->assertSame($matcher, $eventsHandler->getMatcher());

        // retrieve all listeners
        $this->assertArrayHasKey('event.name', $eventsHandler->getListeners());

        // retrieve listeners for one event

        // with registered callbacks
        $this->assertEquals(['event.name' => [$callback]], $eventsHandler->getListeners('event.name'));

        // hook some more events
        $eventsHandler->bind('other-event.name', $callback);
        $eventsHandler->bind('other.event.name', $callback);
        $eventsHandler->bind('yet.other.event.name', $callback);
        $eventsHandler->bind('other.*.name', $callback);
        $eventsHandler->bind('*.name', $callback);
        $eventsHandler->bind('*.event.*', $callback);

        // with no registered callbacks
        $this->assertEquals([], $eventsHandler->getListeners('not.matched'));

        // retrieve listeners depending on a filter
        $this->assertCount(3, $listeners = $eventsHandler->getListeners('event.*'));
        $this->assertArrayHasKey('event.name', $listeners);
        $this->assertArrayHasKey('*.name', $listeners);
        $this->assertArrayHasKey('*.event.*', $listeners);

        $this->assertCount(7, $listeners = $eventsHandler->getListeners('*.name'));
        $this->assertEquals([
            'event.name',
            'other-event.name',
            'other.event.name',
            'yet.other.event.name',
            'other.*.name',
            '*.name',
            '*.event.*'
        ], array_keys($listeners));

        $this->assertCount(3, $listeners = $eventsHandler->getListeners('?.name'));
        $this->assertEquals([
            'event.name',
            'other-event.name',
            '*.name'
        ], array_keys($listeners));

        $this->assertCount(4, $listeners = $eventsHandler->getListeners('?.event.name'));
        $this->assertEquals([
            'other.event.name',
            'other.*.name',
            '*.name',
            '*.event.*'
        ], array_keys($listeners));


    }

    public function dataProviderForTestOtherBindingTests()
    {

        $lambda = function ()
        {
            return 'ok';
        };

        return [
            [
                [
                    'any.event',
                    'any.*',
                    '*.any',
                    'any.other.event'
                ],
                'any.*',
                [
                    'any.event' => [$lambda],
                    'any.*' => [$lambda],
                    '*.any' => [$lambda],
                    'any.other.event' => [$lambda]

                ],
                $lambda
            ],

        ];
    }


    /**
     * @dataProvider dataProviderForTestOtherBindingTests
     */
    public function testOtherBindingTests($eventsToBind, $eventFilter, $eventsBound, $lambda)
    {
        $eventsHandler = new EventsHandler();

        foreach($eventsToBind as $eventToBind)
        {
            $eventsHandler->bind($eventToBind, $lambda);
        }

        $this->assertEquals($eventsBound, $eventsHandler->getListeners($eventFilter));

    }

    public function testBindingWithExactMatchHavePriorityOverWildcardsMatches()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('*', new Callback());
        $eventsHandler->bind('event.name', new Callback());

        $this->assertEquals(['event.name', '*'], array_keys($eventsHandler->getListeners('event.name')));
        $this->assertEquals(['*', 'event.name'], array_keys($eventsHandler->getListeners('*')));

    }

    public function testBindingAnInvalidCallbackThrowsAnException()
    {
        $this->expectsException(function ()
        {
            $eventsHandler = new EventsHandler();
            $eventsHandler->bind('any.event', 'this is not a callable');
        }, Exception::class, 'must be a callable', Exception::EVENT_INVALID_CALLBACK);
    }

    public function testAliasing()
    {
        $callback = $this->getMock('CallbackClass', ['__invoke']);
        $callback->expects($this->exactly(2))->method('__invoke');
        $eventsHandler = new EventsHandler();
        $eventsHandler->bind('*.name', new AliasedCallback('alias', $callback));

        // this second call should fail
        $eventsHandler->bind('event.*', 'alias');

        $eventsHandler->trigger('event.name');
    }

    public function testBindingTwoCallbacksWithSameAliasThrowsAnException()
    {
        $eventsHandler = new EventsHandler();
        $eventsHandler->bind('*', new AliasedCallback('alias', function ()
        {
        }));

        // this second call should fail
        $this->expectsException(function () use ($eventsHandler)
        {
            $eventsHandler->bind('*', new AliasedCallback('alias', function ()
            {
            }));
        }, Exception::class, 'alias', Exception::EVENT_INVALID_CALLBACK);
    }

    public function testCanTriggerAndGetReturnValuesBackEvents()
    {
        $eventsHandler = new EventsHandler();

        $callback = function (Event $event)
        {
            $event->getOrigin()->passedBy['a'] = __FUNCTION__;
            $event->getContext()['other'] = 'context value';

            return 'callback.return.value';
        };

        $eventsHandler->bind('event.name', $callback);
        $eventsHandler->bind('event.name', function (Event $event)
        {
            $event->getOrigin()->passedBy['b'] = __FUNCTION__;
            $event->getOrigin()->context = $event->getContext();

            return $event->getName() . ' has been triggered!';
        });

        $origin = new \stdClass();

        $this->assertCount(2, $result = $eventsHandler->trigger('event.name', $origin, new \ArrayObject(['context' => 'value']))
            ->getResults());
        $this->assertEquals('callback.return.value', $result[0]);
        $this->assertEquals('event.name has been triggered!', $result[1]);
        $this->assertCount(2, $origin->passedBy);
        $this->assertCount(2, $origin->context->toArray());
    }

    public function testNestedEventsCanAccessOtherEvents()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('first.event', function ($event) use ($eventsHandler)
        {
            $result = $eventsHandler->trigger('second.event')->getResults();

            return $result[0];
        });

        $eventsHandler->bind('second.event', function (Event $event)
        {
            return $event->getPrevious();
        });
        $results = $eventsHandler->trigger('first.event')->getResults();
        $this->assertEquals('first.event', $results[0]->getName());
    }

    public function testEventPropagationCanBeStopped()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('test.event', function (Event $event)
        {
            $event->halt();

            return 'returned data';
        });

        $eventsHandler->bind('test.event', function ($event)
        {
            return 'never returned data';
        });

        $event = $eventsHandler->trigger('test.event');

        $this->assertEquals('returned data', $event->getResults()[0]);
    }

    public function testEventBindingWithReplaceMode()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('event.name', function ()
        {
            return 'first bound callback';
        });
        $eventsHandler->bind('event.name', $secondCallback = function ()
        {
            return 'second bound callback';
        }, EventsHandler::BINDING_MODE_REPLACE);

        $listeners = $eventsHandler->getListeners('event.name');

        $this->assertCount(1, $listeners['event.name']);
        $this->assertEquals($secondCallback, $listeners['event.name'][0]);
    }

    public function testEventBindingWithFirstMode()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('event.name', function ()
        {
            return 'first bound callback';
        });
        $eventsHandler->bind('event.name', $secondCallback = function ()
        {
            return 'second bound callback';
        }, EventsHandler::BINDING_MODE_FIRST);

        $listeners = $eventsHandler->getListeners('event.name');

        $this->assertCount(2, $listeners['event.name']);
        $this->assertEquals($secondCallback, $listeners['event.name'][0]);

        // prepend aliased callback
        $thirdCallback = function ()
        {
            return 'third bound callback';
        };
        $eventsHandler->bind('event.name', new AliasedCallback('callback.alias', $thirdCallback), EventsHandler::BINDING_MODE_FIRST);
        $listeners = $eventsHandler->getListeners('event.name');
        $this->assertCount(3, $listeners['event.name']);
        $this->assertEquals($thirdCallback, $listeners['event.name'][0]);


    }

    public function testEventHasExceptionsIfCallbackReturnsAnException()
    {

        $eventsHandler = new EventsHandler();
        $eventsHandler->bind('event.name', function ()
        {
            return new Exception('something went wrong');
        });
        $event = $eventsHandler->trigger('event.name');
        $this->assertTrue($event->isFaulty());

    }

    public function testAnExceptionIsThrownIfCallbackGeneratorComponentDoesNotExist()
    {
        $eventsHandler = new EventsHandler();

        $this->expectsException(function () use ($eventsHandler)
        {
            $eventsHandler->bind('event.name', 'non.existing.callback.generator');
        }, Exception::class);
    }


    function testCallbacksCanBeUnbound()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('any.event', $lambda = function ()
        {
            return 'should not be triggered';
        });

        $eventsHandler->unbind('any.event');

        $this->assertEmpty($eventsHandler->trigger('any.event')->getResults());
    }

    public function testListenersUnbindingSilentlyReturnsIfNoListenerMatchesPattern()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('any.event', $lambda = function ()
        {
            return 'should not be triggered';
        });

        $fluent = $eventsHandler->unbind('other.event');
        $this->assertSame($eventsHandler, $fluent);

        $this->assertAttributeEmpty('unboundListeners', $eventsHandler);
    }

    function testCallbacksCanBeUnboundUsingWildcards()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('any.event', $lambda = function ()
        {
            return 'should not be triggered';
        });

        $eventsHandler->unbind('any.*');

        $this->assertEmpty($eventsHandler->trigger('any.event')->getResults());
    }

    function testCallbacksSetUsingWildcardsCanBeUnbound()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('any.*', $lambda = function ()
        {
            return 'should not be triggered';
        });

        $eventsHandler->unbind('any.event');
        $this->assertEmpty($eventsHandler->trigger('any.event')->getResults());
    }

    function testCallbacksSetUsingWildcardsCanBeUnboundUsingWildcards()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('any.*', new AliasedCallback('listener.alias', $lambda = function ()
        {
            return 'should not be triggered';
        }));

        $eventsHandler->unbind('any.*');

        $this->assertEmpty($eventsHandler->trigger('any.event')->getResults());
        $this->assertAttributeEquals(['any.*' => ['any.*' => ['listener.alias' => $lambda]]], 'unboundListeners', $eventsHandler);
        $this->assertAttributeEquals($eventsHandler->getUnboundListeners(), 'unboundListeners', $eventsHandler);
    }


    public function testCallbacksSetUsingWildcardsCanBeUnboundUsingWildcardsAgain()
    {
        $eventsHandler = new EventsHandler();

        // any.* is matched by the unbind call, so it will be unbound
        // beware: any.event does not match the unbind() pattern, but
        // so does the bound callback
        $eventsHandler->bind('any.*', $lambda = function ()
        {
            return 'should not be triggered';
        });

        $eventsHandler->bind('any.event', $lambda = new AliasedCallback('triggered.callback', function ()
        {
            return 'should be triggered once';
        }));
        $eventsHandler->bind('event.any', $lambda = function ()
        {
            return 'should not be triggered';
        });

        $unbound = $eventsHandler->unbind('*.any');

        $this->assertEquals(['triggered.callback'], $eventsHandler->trigger('any.event')->getResults()->keys()->toArray());
        $this->assertEmpty($eventsHandler->trigger('any.any')->getResults());
    }

    public function testEventsHandlerCanTriggerCustomEvents()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('any.event', $lambda = new AliasedCallback('triggered.callback', function ()
        {
            return 'should be triggered once';
        }));

        $customEvent = new Event;

        $returnedEvent = $eventsHandler->trigger('any.event', null, [], $customEvent);

        $this->assertSame($customEvent, $returnedEvent);
    }

    public function testBindingInvokableClass()
    {
        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('some.event', Callback::class);

        $eventsHandler->trigger('some.event');

        $this->assertTrue(Callback::$triggered);

        $this->expectsException(function () use ($eventsHandler)
        {

            $eventsHandler->bind('other.event', InvalidCallback::class);
            $eventsHandler->trigger('other.event');

        }, Exception::class, null, Exception::EVENT_INVALID_CALLBACK);
    }

    public function testCallbacksAggregatesHandling()
    {
        $event = new Event();

        $callbacks[] = $this->getMockForAbstractClass(AbstractCallback::class);
        $callbacks[] = $this->getMockForAbstractClass(AbstractCallback::class);

        foreach($callbacks as $callback)
        {
            $callback->expects($this->once())->method('run')->with($event)->willReturnSelf();
        }

        $aggregate = new CallbacksAggregate('aggregate', $callbacks);

        $eventsHandler = new EventsHandler();

        $eventsHandler->bind('some.event', $aggregate);

        $eventsHandler->bind('some.event', function ()
        {
        });
        $eventsHandler->trigger('some.event', null, [], $event);

        $this->assertEquals(['aggregate.0', 'aggregate.1', 0], $event->getResults()->keys()->toArray());

    }

    public function testServiceReferenceCanBeUsedAsCallback()
    {
        $factory = (new ServicesFactory())->registerService(new ClassServiceSpecs('injector', Injector::class));
        $eventsHanlder = (new EventsHandler())->setServicesFactory($factory);
        $injector = $factory->get('injector');

        $eventsHanlder->bind('some.event', new ServiceReference('injector'));

        $this->assertSame($injector, $factory->get($eventsHanlder->getListeners('some.event')['some.event'][0]->getId()));


        $eventsHanlder->trigger('some.event');

        $this->assertEquals(1, Injector::$count);

    }


}


class Injector
{
    static public $count;

    public function __invoke()
    {
        self::$count++;
    }
}


class Callback
{
    public static $triggered = false;

    public function __invoke()
    {
        self::$triggered = true;
    }
}

class InvalidCallback
{

}