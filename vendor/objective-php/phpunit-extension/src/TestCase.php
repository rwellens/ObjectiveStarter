<?php

    namespace ObjectivePHP\PHPUnit;

    class TestCase extends \PHPUnit_Framework_TestCase
    {

        public function expectsException(callable $closure, $exceptionName = '\Exception', $exceptionMessage = null, $exceptionCode = null)
        {
            try
            {
                $closure();
                $this->fail('Failed asserting that ' . sprintf(
                    'exception of type "%s" is thrown',
                    $exceptionName
                ));
            }
            catch(\PHPUnit_Framework_AssertionFailedError $e)
            {
                // propagate Exception emitted from $this->fail()
                // without considering it as user exception in
                // the catch block below
                throw $e;
            }
            catch (\Exception $e)
            {
                if($e instanceof $exceptionName)
                {
                    if($exceptionMessage && strpos($e->getMessage(), $exceptionMessage) === false)
                    {
                        $this->fail('Failed asserting that ' . sprintf(
                            "exception message '%s' contains '%s'",
                            $e->getMessage(),
                            $exceptionMessage
                        ));
                    }

                    if ($exceptionCode && $e->getCode() != $exceptionCode)
                    {
                        $this->fail('Failed asserting that ' . sprintf(
                            '%s is equal to expected exception code %s',
                            $e->getCode(),
                            $exceptionCode
                        ));
                    }

                    $this->addToAssertionCount(1);
                }
                else
                {
                    $this->fail('Failed asserting that ' . sprintf(
                        'exception of type "%s" matches expected exception "%s"',
                        get_class($e),
                        $exceptionName
                    ));
                }
            }
        }

        /**
         * Helper to set any object internal attribute value
         *
         * This prevent from relying on a setter when testing a getter
         *
         * @param $instance
         * @param $property
         * @param $value
         *
         * @return $this
         */
        public function setObjectAttribute($instance, $property, $value)
        {
            $reflection = new \ReflectionObject($instance);
            $property   = $reflection->getProperty($property);
            $property->setAccessible(true);
            $property->setValue($instance, $value);

            return $this;
        }
    }