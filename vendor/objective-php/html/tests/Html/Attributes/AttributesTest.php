<?php
    
    namespace Tests\ObjectivePHP\HtmlAttributes;

    use ObjectivePHP\Html\Attributes\Attributes;
    use ObjectivePHP\PHPUnit\TestCase;
    use ObjectivePHP\Primitives\Collection\Collection;

    class AttributesTest extends TestCase
    {

        public function testInitialization()
        {
            $attribs = new Attributes(['x' => 'y']);
            $this->assertTrue($attribs->has('x'));
            $this->assertEquals('y', $attribs['x']);

        }

        public function testToString()
        {
            $attribs = new Attributes();

            $attribs->set('a', 'x')->set('b', ['y', 'z'])->append('disabled');

            $this->assertEquals('a="x" b="y z" disabled', (string) $attribs);
        }

        public function testBooleanAttributes()
        {
            $attribs = new Attributes();

            $attribs->set('a', true)->set('b', false)->append('disabled');

            $this->assertEquals('a disabled', (string) $attribs);

            $attribs->set('a', false);
            $this->assertEquals('disabled', (string) $attribs);
        }


        public function testMultiValuesAttributes()
        {
            $values = new Collection(['a', 'b']);
            $attribs = new Attributes(['x' => $values]);

            $this->assertInstanceOf(Collection::class, $attribs['x']);
            $this->assertSame($values, $attribs['x']);

            $attribs->set('x', $otherValues = new Collection(['y', 'z']));

            $this->assertInstanceOf(Collection::class, $attribs['x']);
            $this->assertSame($values, $attribs['x']);


        }

    }