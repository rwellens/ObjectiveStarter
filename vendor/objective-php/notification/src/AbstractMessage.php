<?php

namespace ObjectivePHP\Notification;


use ObjectivePHP\Primitives\Collection\Collection;
use ObjectivePHP\Primitives\String\Str;

class AbstractMessage implements MessageInterface
{

    /**
     * @var string
     */
    protected $type;

    /**
     * @var Collection
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