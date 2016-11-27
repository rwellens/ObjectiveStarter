<?php

    namespace ObjectivePHP\Html\Message;
    
    
    use ObjectivePHP\Primitives\String\Str;

    class AbstractMessage implements MessageInterface
    {

        protected $type;

        /**
         * @var Str
         */
        protected $message;

        public function __construct($message)
        {
            $this->message = Str::cast($message);
        }

        public function getType()
        {
            return $this->type;
        }

        public function __toString()
        {
            return (string) $this->message;
        }

    }