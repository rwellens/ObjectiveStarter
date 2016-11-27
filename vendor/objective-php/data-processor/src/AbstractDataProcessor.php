<?php
    namespace ObjectivePHP\DataProcessor;
    
    use ObjectivePHP\Primitives\Collection\Collection;
    use ObjectivePHP\Primitives\String\Str;
    use ObjectivePHP\Notification;

    /**
     * Class AbstractDataProcessor
     *
     * @package ObjectivePHP\DataProcessor
     */
    abstract class AbstractDataProcessor implements DataProcessorInterface
    {

        /**
         * Error message templates
         *
         * @var Collection
         */
        protected $messages;

        /**
         * AbstractDataProcessor constructor.
         */
        public function __construct()
        {
            $this->messages      = new Collection;
        }

        /**
         * Delegated constructor
         */
        public function init()
        {

        }

        /**
         * @return Collection
         */
        public function getMessages()
        {
            return $this->messages;
        }

        /**
         * @return Str
         */
        public function getMessage($code, $values = [])
        {
            $message = Str::cast($this->getMessages()->get($code));

            foreach ($values as $placeHolder => $value)
            {
                $message->setVariable($placeHolder, $value);
            }

            return $message;
        }

        /**
         * @param string $message
         *
         * @return $this
         */
        public function setMessage($code, $message) : self
        {
            $this->messages[$code] = $message;

            return $this;
        }

        /**
         * Proxy direct invocation to process
         *
         * @param $value
         *
         * @return mixed
         */
        public function __invoke($value)
        {
            return $this->process($value);
        }
    }