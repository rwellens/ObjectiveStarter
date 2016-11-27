<?php
    namespace ObjectivePHP\Events;

    use ObjectivePHP\Primitives\Collection\Collection;
    use ObjectivePHP\Primitives\String\Str;

    class Event implements EventInterface
    {

        const WAITING   = 'waiting';
        const TRIGGERED = 'triggered';
        const FINISHED  = 'finished';
        const HALTED    = 'halted';

        protected $name;

        protected $previous;

        protected $origin;

        /**
         * @var Collection
         */
        protected $context;

        /**
         * @var Collection
         */
        protected $results;

        /**
         * @var Collection
         */
        protected $exceptions;

        protected $status     = self::WAITING;


        public function __construct()
        {
            $this->results = new Collection();
            $this->context = new Collection();
            $this->exceptions = (new Collection())->restrictTo(\Exception::class, false);
        }

        /**
         * Event name setter
         *
         * @param string $name The event name
         */
        public function setName($name)
        {
            $name = Str::cast($name);

            $this->name = $name->lower();

            return $this;
        }

        /**
         * @return Str
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * Event origin setter
         *
         * @param mixed $origin Source of the event (usually the object from which the event was triggered)
         */
        public function setOrigin($origin)
        {
            // origin overwriting is forbidden
            if (!is_null($this->origin))
            {
                throw new Exception('Overwriting origin of an event is forbidden', Exception::EVENT_ORIGIN_IS_IMMUTABLE);
            }

            $this->origin = $origin;

            // update status to reflect event triggering
            // => setting the origin means that the event has been triggered
            $this->status = self::TRIGGERED;

            return $this;
        }

        /**
         * @return mixed
         */
        public function getOrigin()
        {
            return $this->origin;
        }

        /**
         * Event status getter (no mutator on this property)
         */
        public function getStatus()
        {
            return $this->status;
        }

        /**
         * Event context setter
         *
         * @param array|\ArrayObject|Collection $context context parameters
         * @param string                        $mode
         */
        public function setContext($context)
        {
            if (!is_array($context) && !$context instanceof \ArrayObject && !$context instanceof \Iterator)
            {
                throw new Exception('Unexpected value type for context', Exception::EVENT_INVALID_CONTEXT);
            }

            $context = Collection::cast($context);

            $this->context = $context;

            return $this;
        }

        /**
         * @return Collection
         */
        public function getContext()
        {
            return $this->context;
        }

        /**
         * Event predecessor accessor
         *
         * This set/returns the event from which this event has been triggered
         *
         * @param Event $event Previous event
         *
         * @return Event
         */
        public function setPrevious(EventInterface $event = null)
        {
            $this->previous = $event;

            return $this;

        }

        /**
         * @return mixed
         */
        public function getPrevious()
        {
            return $this->previous;
        }

        /**
         * Stops event propagation
         */
        public function halt()
        {
            $this->status = self::HALTED;

            return $this;
        }

        /**
         * Indicates whether the current event has stopped event propagation
         *
         * @return bool
         */
        public function isHalted()
        {
            return $this->status == self::HALTED;
        }


        public function getResults()
        {
            if ($this->getStatus() == self::WAITING)
            {
                throw new Exception('Event results cannot be retrieved before it has been triggered', Exception::EVENT_IS_NOT_TRIGGERED_YET);
            }

            return $this->results;
        }

        public function setResult($callbackName, $result)
        {
            if ($this->getStatus() == self::WAITING)
            {
                throw new Exception('Event result can be set once event has been triggered only', Exception::EVENT_IS_NOT_TRIGGERED_YET);
            }

            $this->results[$callbackName] = $result;

            return $this;
        }

        public function getExceptions()
        {
            if ($this->getStatus() == self::WAITING)
            {
                throw new Exception('Event exceptions cannot be retrieved before it has been triggered', Exception::EVENT_IS_NOT_TRIGGERED_YET);
            }

            return $this->exceptions;
        }

        public function setException($callbackName, \Exception $exception)
        {
            if ($this->getStatus() == self::WAITING)
            {
                throw new Exception('Event exception can be set once event has been triggered only', Exception::EVENT_IS_NOT_TRIGGERED_YET);
            }

            $this->getExceptions()[$callbackName] = $exception;

            return $this;
        }

        public function isFaulty()
        {
            return !$this->getExceptions()->isEmpty();
        }

    }