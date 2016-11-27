<?php

    namespace ObjectivePHP\Events\Callback;
    
    
    use ObjectivePHP\Events\EventInterface;

    abstract class AbstractCallback implements CallbackInterface
    {
        public function __invoke(EventInterface $event)
        {
            return $this->run($event);
        }

        abstract public function run(EventInterface $event);

    }