<?php

    namespace Test\ObjectivePHP\DataProcessor;
    
    
    use ObjectivePHP\DataProcessor\DataProcessingException;
    use ObjectivePHP\DataProcessor\DateProcessor;
    use ObjectivePHP\DataProcessor\StringProcessor;
    use ObjectivePHP\PHPUnit\TestCase;
    use ObjectivePHP\Primitives\String\Str;

    class StringProcessorTest extends TestCase
    {
        public function testStringProcessing()
        {

            $stringProcessor = new StringProcessor();

            $processedString = $stringProcessor->process('this is a native string');

            $this->assertInstanceOf(Str::class, $processedString);

            $this->assertEquals('this is a native string', $processedString);

        }


    }