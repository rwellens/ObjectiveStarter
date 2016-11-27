<?php

    namespace ObjectivePHP\DataProcessor;


    class NumericProcessor extends AbstractDataProcessor
    {

        const NOT_A_NUMBER = 'NaN';

        /**
         * Initializer
         */
        public function init()
        {
            // set specific messages
            $this->setMessage(self::NOT_A_NUMBER, 'The processed value cannot be converted to any numeric type');
        }

        /**
         * @param $value
         *
         * @return int|float
         */
        public function process($value)
        {
            if(!is_numeric($value))
            {
                throw new DataProcessingException($this->getMessage(self::NOT_A_NUMBER));
            }

            return $value / 1;
        }

    }