<?php

namespace Tests\ObjectivePHP\Html\Message;


use ObjectivePHP\Html\Message\Alert;
use ObjectivePHP\PHPUnit\TestCase;

class AbstractMessageTest extends TestCase
{

    public function testToStringImplementation()
    {
        $message = new Alert('test');

        $this->assertEquals('test', (string) $message);
    }

}