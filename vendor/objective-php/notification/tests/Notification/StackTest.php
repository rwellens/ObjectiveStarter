<?php
    namespace Tests\ObjectivePHP\Notification;

    use ObjectivePHP\Notification;
    use ObjectivePHP\PHPUnit\TestCase;


    class NotificationMessageTest extends TestCase
    {

        public static function dataProviderForMessageFiltering()
        {
            return
                [
                    [['x.y', 'x.z', 'a.b'], 'a.*', 1],
                    [['x.y', 'x.z', 'a.b'], 'x.*', 2],
                    [['x.y', 'x.z', 'a.b'], 'x', 0],
                    [['x.y', 'x.z', 'a.b'], 'x.y', 1],
                ];
        }

        public function testAddMessage()
        {
            $message       = new Notification\Info('hello');
            $notifications = new Notification\Stack();

            $notifications->addMessage('data.form', $message);

            $this->assertEquals(['data.form' => $message], $notifications->toArray());
        }

        /**
         * @dataProvider dataProviderForMessageFiltering
         */
        public function testMessageFiltering($messages, $filter, $expected)
        {

            $notifications = new Notification\Stack();

            foreach($messages as $message)
            {
                $notifications->addMessage($message, new Notification\Info(uniqid()));
            }

            $this->assertEquals($expected, $notifications->for($filter)->count());

        }


        public function testMessageCounting()
        {
            $notifications = new Notification\Stack();

            $notifications->addMessage(uniqid(), new Notification\Info(uniqid()));
            $notifications->addMessage(uniqid(), new Notification\Info(uniqid()));
            $notifications->addMessage(uniqid(), new Notification\Alert(uniqid()));
            $notifications->addMessage(uniqid(), new Notification\Info(uniqid()));


            $this->assertEquals(3, $notifications->count('info'));
            $this->assertEquals(1, $notifications->count('danger'));

        }
    }
