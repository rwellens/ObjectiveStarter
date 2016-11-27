<?php

    namespace ObjectivePHP\Events\Callback;

    /**
     * Class AliasedCallback
     *
     * Binding an aliased callback will make EventsHandler use the callback
     * alias to identify the return of this callback
     *
     * @package ObjectivePHP\Events\Callback
     */
    class AliasedCallback
    {
        /**
         * @var string Callback alias
         */
        protected $alias;

        /**
         * @var mixed Callback (callable or invokable class name)
         */
        protected $callback;

        /**
         * Constructor
         *
         * @param $alias
         * @param $callback
         */
        public function __construct($alias, $callback)
        {
            $this->setAlias($alias);
            $this->setCallback($callback);
        }

        /**
         * @return string
         */
        public function getAlias()
        {
            return $this->alias;
        }

        /**
         * @param mixed $alias
         *
         * @return $this
         */
        public function setAlias($alias)
        {
            $this->alias = $alias;

            return $this;
        }

        /**
         * @return mixed
         */
        public function getCallback()
        {
            return $this->callback;
        }

        /**
         * @param mixed $callback
         *
         * @return $this
         */
        public function setCallback($callback)
        {
            $this->callback = $callback;

            return $this;
        }

    }