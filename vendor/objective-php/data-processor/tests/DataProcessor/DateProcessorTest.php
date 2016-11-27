<?php

    namespace Test\ObjectivePHP\DataProcessor;
    
    
    use ObjectivePHP\DataProcessor\DataProcessingException;
    use ObjectivePHP\DataProcessor\DateProcessor;
    use ObjectivePHP\PHPUnit\TestCase;

    class DateProcessorTest extends TestCase
    {
        public function testDateProcessing()
        {

            $date = '2008-11-28';

            $dateProcessor = new DateProcessor();

            $processedDate = $dateProcessor->process($date);

            $this->assertInstanceOf(\DateTime::class, $processedDate);

            $this->assertEquals('2008-11-28', $processedDate->format('Y-m-d'));

        }


        public function testDateProcessingWithFormatSpecifiedAtInstanciation()
        {

            $date = '28/11/2008';

            $dateProcessor = new DateProcessor('d/m/Y');

            $processedDate = $dateProcessor->process($date);

            $this->assertInstanceOf(\DateTime::class, $processedDate);

            $this->assertEquals('2008-11-28', $processedDate->format('Y-m-d'));
        }

        public function testDateProcessingWithFormatSpecifiedAtRuntime()
        {

            $date = '28/11/2008';

            $dateProcessor = new DateProcessor();

            $processedDate = $dateProcessor->process($date, 'd/m/Y');

            $this->assertInstanceOf(\DateTime::class, $processedDate);

            $this->assertEquals('2008-11-28', $processedDate->format('Y-m-d'));
        }

        public function testDateProcessingFailsIfFormatDoesNotMatch()
        {

            $date = '28/11/2008';

            $dateProcessor = new DateProcessor();

            $this->expectsException(function() use ($dateProcessor, $date) {
                $dateProcessor->process($date);
            }, DataProcessingException::class);

        }
    }