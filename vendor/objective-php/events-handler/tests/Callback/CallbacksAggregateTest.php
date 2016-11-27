<?php

    namespace Tests\ObjectivePHP\Events\Callback;

    use ObjectivePHP\Events\Callback\AbstractCallback;
    use ObjectivePHP\Events\Callback\CallbacksAggregate;
    use ObjectivePHP\Events\Event;
    use ObjectivePHP\Primitives\Collection\Collection;

    class CallbacksAggregateTest extends \PHPUnit_Framework_TestCase
    {

        public function testCallbacksAreSetUsingConstructorParametersList()
        {
            $aggregate = new CallbacksAggregate('aggregate', $lambda = function () {}, $otherLambda = function() {});

            $this->assertEquals(Collection::cast([$lambda, $otherLambda]), $aggregate->getCallbacks());
        }

        public function testCallbacksAreSetUsingAnArrayAsConstructorParam()
        {
            $aggregate = new CallbacksAggregate('aggreagate', [$lambda = function () {}, $otherLambda = function() {}]);

            $this->assertEquals(Collection::cast([$lambda, $otherLambda]), $aggregate->getCallbacks());
        }

    }