<?php

namespace Tests\ObjectivePHP\Html\Message;

use ObjectivePHP\Html\Message\Alert;
use ObjectivePHP\Html\Message\Info;
use ObjectivePHP\Html\Message\MessageInterface;
use ObjectivePHP\Html\Message\MessageStack;
use ObjectivePHP\PHPUnit\TestCase;
use ObjectivePHP\Primitives\Collection\Validator\ObjectValidator;

class MessageStackTest extends TestCase
{

    public function testMessageStackInitialization()
    {
        $stack = new MessageStack();

        $this->assertCount(1, $stack->getValidators());
        $validator = $stack->getValidators()[0];
        $this->assertInstanceOf(ObjectValidator::class, $validator);
        $this->assertAttributeEquals(MessageInterface::class, 'class', $validator);
    }

    public function testMessageCounting()
    {
        $stack = new MessageStack();
        $stack[] = new Alert('test alert');
        $stack[] = new Alert('other test alert');
        $stack[] = new Info('test info');

        $this->assertCount(3, $stack);
        $this->assertEquals(2, $stack->count('danger'));
        $this->assertEquals(1, $stack->count('info'));
    }

}