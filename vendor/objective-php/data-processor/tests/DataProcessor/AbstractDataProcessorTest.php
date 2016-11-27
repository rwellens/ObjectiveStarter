<?php

    namespace Test\ObjectivePHP\DataProcessor;
    
    
    use ObjectivePHP\DataProcessor\AbstractDataProcessor;
    use ObjectivePHP\PHPUnit\TestCase;
    use ObjectivePHP\Primitives\String\Str;

    class AbstractDataProcessorTest extends TestCase
    {
        public function testMessagesHandling()
        {

            $processor = $this->getMockForAbstractClass(AbstractDataProcessor::class);

            $processor->setMessage('test', 'test message');
            $this->assertEquals(new Str('test message'), $processor->getMessage('test'));

            // with placeholder
            $processor->setMessage('test', 'test :var');
            $this->assertEquals('test message', $processor->getMessage('test', ['var' => 'message']));

        }

    }