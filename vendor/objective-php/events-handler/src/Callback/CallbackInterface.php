<?php

    namespace ObjectivePHP\Events\Callback;
    
    
    use ObjectivePHP\Events\EventInterface;

    interface CallbackInterface
    {

        public function __invoke(EventInterface $event);

    }