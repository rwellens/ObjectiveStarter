<?php

    namespace Test\ObjectivePHP\DataProcessor;
    
    
    use ObjectivePHP\DataProcessor\DataProcessingException;
    use ObjectivePHP\DataProcessor\NumericProcessor;
    use ObjectivePHP\PHPUnit\TestCase;

    class NumericProcessorTest extends TestCase
    {
        public function testNumericProcessing()
        {

            $numericProcessor = new NumericProcessor();

            $this->assertTrue(is_int($numericProcessor->process('12')));
            $this->assertTrue(is_float($numericProcessor->process('12.3')));

        }

        public function testNumericProcessingFailsWithNonNumericValues()
        {
            $this->expectsException(function() {
                $numericProcessor = new NumericProcessor();
                $numericProcessor->process('this is not a number');
            }, DataProcessingException::class);
        }

    }