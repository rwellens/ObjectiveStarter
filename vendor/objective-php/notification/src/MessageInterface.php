<?php

namespace ObjectivePHP\Notification;


interface MessageInterface
{
    public function __construct($message);

    public function getType();
}
