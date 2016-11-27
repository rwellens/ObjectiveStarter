<?php

    namespace ObjectivePHP\Html\Message;
    
    
    interface MessageInterface
    {
        public function __construct($message);

        public function getType();
    }