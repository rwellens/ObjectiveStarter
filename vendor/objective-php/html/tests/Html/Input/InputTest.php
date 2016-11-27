<?php

    namespace Tests\ObjectivePHP\Html\Input;

    use ObjectivePHP\Html\Tag\Input\Input;
    use ObjectivePHP\Html\Tag\Input\Select;
    use ObjectivePHP\PHPUnit\TestCase;

    class InputTest extends TestCase
    {
        public function testTextDefaultValueSetting()
        {

            Input::setData(['test' => 'test field value']);

            $tag = Input::text('test');


            $renderedTag = (string) $tag;

            $this->assertEquals('<input type="text" id="test" name="test" value="test field value">', $renderedTag);

        }

        public function testDateDefaultValueSetting()
        {

            $now = new \DateTime();
            Input::setData(['test' => $now]);

            $tag = Input::date('test');


            $renderedTag = (string) $tag;

            $this->assertEquals('<input type="date" id="test" name="test" value="'. $now->format($tag::getDateDefaultFormat())  .'">', $renderedTag);

        }

        public function testCheckboxDefaultValueSetting()
        {

            $tag = Input::checkbox('test');

            $renderedTag = (string) $tag;
            $this->assertEquals('<input type="checkbox" id="test" name="test" value="1">', $renderedTag);

            Input::setData(['test' => '1']);

            $renderedTag = (string) $tag;
            $this->assertEquals('<input type="checkbox" id="test" name="test" value="1" checked>', $renderedTag);

        }

        public function testMultiCheckboxDefaultValueSetting()
        {

            $tag = Input::checkbox('test')->name('test[]');

            $renderedTag = (string) $tag;
            $this->assertEquals('<input type="checkbox" id="test" name="test[]" value="1">', $renderedTag);

            Input::setData(['test' => ['1', '3']]);

            $renderedTag = (string) $tag;
            $this->assertEquals('<input type="checkbox" id="test" name="test[]" value="1" checked>', $renderedTag);

            $tag2 = Input::checkbox('test', 2)->name('test[]');
            $renderedTag2 = (string) $tag2;
            $this->assertEquals('<input type="checkbox" id="test" name="test[]" value="2">', $renderedTag2);

            $tag3 = Input::checkbox('test', 3)->name('test[]');
            $renderedTag3 = (string) $tag3;
            $this->assertEquals('<input type="checkbox" id="test" name="test[]" value="3" checked>', $renderedTag3);


        }

        public function testRadioDefaultValueSetting()
        {

            $tag = Input::radio('test', 'yes');


            $renderedTag = (string) $tag;
            $this->assertEquals('<input type="radio" id="test" name="test" value="yes">', $renderedTag);

            Input::setData(['test' => 'yes']);

            $renderedTag = (string) $tag;
            $this->assertEquals('<input type="radio" id="test" name="test" value="yes" checked>', $renderedTag);

        }

        public function testTextareaDefaultValueSetting()
        {
            $tag = Input::textarea('test');

            $renderedTag = (string) $tag;
            $this->assertEquals('<textarea id="test" name="test"></textarea>', $renderedTag);
        }

        public function testSelectDefaultValueSetting()
        {
            $select = Input::select('test');

            $this->assertInstanceOf(Select::class, $select);

            $select->addOption(Input::option('option1', 'First option'));
            $select->addOption('option2');

            $renderedTag = (string) $select;
            $this->assertEquals('<select id="test" name="test"><option value="option1">First option</option> <option>option2</option></select>', $renderedTag);


            Input::setData(['test' => 'option2']);
            $renderedTag = (string) $select;
            $this->assertEquals('<select id="test" name="test"><option value="option1">First option</option> <option selected>option2</option></select>', $renderedTag);

            // select multiple
            Input::setData(['test' => ['option1', 'option2']]);
            $renderedTag = (string) $select;
            $this->assertEquals('<select id="test" name="test"><option value="option1" selected>First option</option> <option selected>option2</option></select>', $renderedTag);


        }

        public function testValueShortcut()
        {
            $input = Input::text('test')->value('default value')->disableAutoDump();

            $this->assertEquals($input->value(), $input->getAttribute('value'));
            $this->assertEquals('default value', $input->getAttribute('value'));

        }

        public function tearDown()
        {
            Input::setData([]);

            parent::tearDown();
        }
    }