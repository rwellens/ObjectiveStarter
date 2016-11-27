<?php
    /**
     * Created by PhpStorm.
     * User: gauthier
     * Date: 14/08/15
     * Time: 18:40
     */
    
    namespace ObjectivePHP\Events;
    
    
    use ObjectivePHP\Primitives\Collection\Collection;

    interface EventInterface
    {

        public function setName($name);

        public function getName();

        public function setOrigin($origin);

        public function getOrigin();

        public function setPrevious(EventInterface $previous);

        public function getPrevious();

        /**
         * @return Collection
         */
        public function getResults();

        /**
         * @return Collection
         */
        public function getContext();

        public function setContext($context);

        public function getStatus();

        /**
         * @return Collection
         */
        public function getExceptions();

        public function halt();

        public function isHalted();

        public function isFaulty();




    }