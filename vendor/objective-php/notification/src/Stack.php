<?php

namespace ObjectivePHP\Notification;


use ObjectivePHP\Matcher\Matcher;
use ObjectivePHP\Primitives\Collection\Collection;

class Stack extends Collection
{

    /**
     * @var Matcher
     */
    protected $matcher;

    /**
     * Stack constructor.
     *
     * @param array $messages
     */
    public function __construct($messages = [])
    {
        parent::__construct($messages);

        $this->restrictTo(MessageInterface::class);

    }

    /**
     * @param string           $key
     * @param MessageInterface $message
     *
     * @return $this
     * @throws \ObjectivePHP\Primitives\Exception
     */
    public function addMessage($key, MessageInterface $message)
    {
        $this->set($key, $message);

        return $this;
    }

    /**
     * @param string|null $type
     *
     * @return int
     * @throws \ObjectivePHP\Primitives\Exception
     */
    public function count($type = null)
    {
        if(is_null($type))
        {
            $count = parent::count();
        }
        else
        {
            $count = 0;
            $this->each(
                function (MessageInterface $message) use (&$count, $type)
                {
                    if($type == $message->getType()) $count ++;
                }
            );
        }

        return $count;
    }

    /**
     * @param $filter
     */
    public function for($filter)
    {
        return (clone $this)->filter(function($value, $key) use($filter) {
            return $this->getMatcher()->match($filter, $key);
        });
    }

    /**
     * @return Matcher
     */
    public function getMatcher()
    {

        if(is_null($this->matcher))
        {
            $this->matcher = new Matcher();
        }

        return $this->matcher;
    }

    /**
     * @param Matcher $matcher
     *
     * @return $this
     */
    public function setMatcher(Matcher $matcher)
    {
        $this->matcher = $matcher;

        return $this;
    }

}