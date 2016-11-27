<?php

    namespace ObjectivePHP\DataProcessor;
    
    

    class DateProcessor extends AbstractDataProcessor
    {

        const INVALID_FORMAT = 'invalid-format';


        protected $format;

        public function __construct($format = 'Y-m-d')
        {
            parent::__construct();
            $this->setFormat($format);

            $this->setMessage(self::INVALID_FORMAT, 'The parameter value does not match expected date format (":format")');
        }

        public function process($value, $format =  null) : \DateTime
        {

            $date = \DateTime::createFromFormat($format ?: $this->getFormat(), $value);

            if($date === false)
            {
                throw new DataProcessingException($this->getMessage(self::INVALID_FORMAT, ['format' => $this->getFormat()]));
            }

            return $date;

        }


        /**
         * @return string
         */
        public function getFormat() : string
        {
            return $this->format;
        }

        /**
         * @param string $format
         *
         * @return $this
         */
        public function setFormat(string $format) : self
        {
            $this->format = $format;

            return $this;
        }


    }