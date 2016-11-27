<?php
namespace Tests\ObjectivePHP\Matcher;

use ObjectivePHP\Matcher\Exception;
use ObjectivePHP\Matcher\Matcher;
use ObjectivePHP\PHPUnit\TestCase;


class MatcherTest extends TestCase
{

    /**
     * @dataProvider dataProviderForTestInvalidMatch
     */
    public function testInvalidMatch($filter, $reference)
    {
        $matcher = new Matcher();
        $this->assertFalse($matcher->match($filter, $reference));
    }

    /**
     * @dataProvider dataProviderForTestValidMatch
     */
    public function testValidMatch($filter, $reference)
    {
        $matcher = new Matcher;
        $this->assertTrue($matcher->match($filter, $reference));
    }

    public function testAlternateSeparator()
    {
        $matcher = new Matcher;
        $matcher->setSeparator('\\');
        $this->assertEquals('\\', $matcher->getSeparator());
        $this->assertTrue($matcher->match('*\event\test', 'namespace\event\test'));
    }

    public function testAlternativesExtractor()
    {
        $matcher = new Matcher;
        $this->assertEquals(['name', 'alternate'], $matcher->extractAlternatives('[name|alternate]'));
        $this->assertEquals(['name', 'alternate', 'last'], $matcher->extractAlternatives('[name|alternate||last]'));

        // missing trailing ']'
        $this->expectsException(function() use($matcher) {
            $matcher->extractAlternatives('[invalid|syntax');
        }, Exception::class);

        // no alternatives
        $this->expectsException(function() use($matcher) {
            $matcher->extractAlternatives('[]');
        }, Exception::class);

        // no alternatives
        $this->expectsException(function() use($matcher) {
            $matcher->extractAlternatives('[|]');
        }, Exception::class);
    }



    public function dataProviderForTestValidMatch()
    {
        return
        [
            ['reference', 'reference'],
            [['multiple', 'reference'], 'multiple.reference'],
            ['multiple.reference', ['multiple', 'reference']],
            ['*', 'reference'],
            ['reference', '*'],
            ['.reference', 'reference'],
            ['*.event', 'event.test.*'],
            ['*.event', 'name.space.event'],
            ['*.event', '*.event'],
            ['*.event.test', 'namespace.event.test'],
            ['*.event.*', 'namespace.event.test'],
            ['*.event', 'name.space.*'],
            ['*.*.event.*', 'namespace.event.test'],
            ['*.*.event.*', 'namespace.?.test'],
            ['*.*.event', 'namespace.event'],
            ['any.event', '*.event'],
            ['any.other.event', '*.?.event'],
            [['any', 'other', 'event'], '*.?.event'],
            ['namespace.event.test', '*.event.test'],
            ['event.name', 'event.?'],
            ['event.?', 'event.name'],
            [['event.name', '*'], ['event.name', uniqid()]],
            ['event.name.*', 'event.?.post'],
            ['event.name.post', 'event.?.post'],
            ['?.*.?', '?.?.?.?.*'],
            ['event.[name|alternate]', 'event.name'],
            ['event.[name|alternate]', 'event.alternate'],
            ['event.name', 'event.[name|alternate]'],
            ['event.[alternate|other]', 'event.[name|alternate]'],
            ['event.[alternate|other|?]', 'event.[any]'],
            ['event.[alternate|other|?]', 'event.what-ever'],
            ['services.*.error', 'services.x.error'],
            ['services.*.error', 'services.x.y.error'],
            ['services.*.error', 'services.[x|y].error'],
            ['!service', 'not-service'],
            ['service', '!not-service'],
            ['service', '![x|y]'],
            ['.', '.'],

        ];
    }

    public function dataProviderForTestInvalidMatch()
    {
       return
       [
           ['some.event', 'some.callback'],
           ['event.?', 'event.some.thing'],
           ['*.event', 'name.space.other'],
           ['?.*.event', 'name.?.other'],
           ['event.*', 'namespace.event'],
           ['any.event', '*.?.event'],
           ['event.name', 'event.?.post'],
           ['event.[name|alternate]', 'event.other'],
           ['services.*.error', 'services.something.something-else'],
           ['services.*.any', 'services.*.something-else'],
           ['services.something.something-else', 'services.*.error'],
           ['!service', '!not-service'],
           ['service', '![x|y|service]'],
           ['service.any', '![x|y|service].*'],
       ];
    }
}
