<?php

    namespace ObjectivePHP\DataProcessor;
    

    use ObjectivePHP\Primitives\Collection\Collection;

    interface DataProcessorInterface
    {

        /**
         * Process a value
         *
         * The processed value will be stored as parameter value
         *
         * @param mixed $value
         *
         * @return mixed
         */
        public function process($value);

        /**
         * @return Collection
         */
        public function getMessages();

        /**
         * Get error message for given error code
         *
         * @param       $code
         * @param array $variables Variables to be injected into message string (Str instance)
         *
         * @return mixed
         */
        public function getMessage($code, $variables = []);

        /**
         * Set error message for given error code
         *
         * @param mixed  $code
         * @param string $message
         *
         * @return mixed
         */
        public function setMessage($code, $message);

    }