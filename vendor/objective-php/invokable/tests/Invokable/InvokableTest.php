<?php
    /**
     * This file is part of the Objective PHP project
     *
     * More info about Objective PHP on www.objective-php.org
     *
     * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
     */
    
    namespace Tests\Invokable;
    
    
    use ObjectivePHP\Application\ApplicationInterface;
    use ObjectivePHP\Invokable\Exception;
    use ObjectivePHP\Invokable\Invokable;
    use ObjectivePHP\PHPUnit\TestCase;
    use ObjectivePHP\ServicesFactory\ServiceReference;
    use ObjectivePHP\ServicesFactory\ServicesFactory;

    class InvokableTest extends TestCase
    {
        /**
         * @dataProvider dataProviderFor_testAnyCallableIsReturnedAsIs
         */
        public function testAnyCallableIsReturnedAsIs($invokable)
        {
            $callable = (new Invokable($invokable))->getCallable();

            if(is_object($invokable))
            {
                $this->assertSame($callable, $invokable);
            }
            else
            {
                $this->assertEquals($callable, $invokable);
            }
        }

        public function dataProviderFor_testAnyCallableIsReturnedAsIs()
        {
            return [
              [[StaticMethodProvider::class, 'method']],
              [[new DynamicMethodProvider(), 'method']],
              [function() {}],
              [new InvokableClass()],
            ];
        }


        public function testClassNameCanBeUsedAsInvokable()
        {
            $invokable = new Invokable(InvokableClass::class);

            $this->assertInstanceOf(InvokableClass::class, $invokable->getCallable());


        }

        public function testNotInvokableClassException()
        {
            // an exception is thrown if class does not implements __invoke
            try
            {
                $invokable = new Invokable(DynamicMethodProvider::class);
                $invokable->getCallable();
            } catch (Exception $e)
            {

            }
            $this->expectsException(function () use ($e)
            {
                throw $e;
            }, Exception::class, null, Exception::FAILED_RUNNING_OPERATION);

            $this->expectsException(function () use ($e)
            {
                throw $e->getPrevious();
            }, Exception::class, null, Exception::CLASS_IS_NOT_INVOKABLE);
        }

        public function testNotExistingClassException()
        {
            // an exception is thrown if class does not implements __invoke
            try
            {
                $invokable = new Invokable('not_existing_class');
                $invokable->getCallable();
            } catch (Exception $e)
            {

            }
            $this->expectsException(function () use ($e)
            {
                throw $e;
            }, Exception::class, null, Exception::FAILED_RUNNING_OPERATION);

            $this->expectsException(function () use ($e)
            {
                throw $e->getPrevious();
            }, Exception::class, null, Exception::CLASS_DOES_NO_EXIST);
        }

        public function testServiceReferenceInvokableTriggerServiceBuilding()
        {
            $service = new InvokableClass();
            $servicesFactory = $this->getMock(ServicesFactory::class);
            $servicesFactory->expects($this->once())->method('has')->with('service.id')->willReturn(true);
            $servicesFactory->expects($this->once())->method('get')->with('service.id')->willReturn($service);

            $application = $this->getMock(ApplicationInterface::class);
            $application->method('getServicesFactory')->willReturn($servicesFactory);

            $invokable = new Invokable(new ServiceReference('service.id'));
            $invokable->setApplication($application);

            $this->assertSame($service, $invokable->getCallable());
        }

        public function testUnregisteredReferencedServiceException()
        {
            $servicesFactory = $this->getMock(ServicesFactory::class);
            $servicesFactory->expects($this->once())->method('has')->with('service.id')->willReturn(false);

            $application = $this->getMock(ApplicationInterface::class);
            $application->method('getServicesFactory')->willReturn($servicesFactory);

            $invokable = new Invokable(new ServiceReference('service.id'));
            $invokable->setApplication($application);


            try
            {
                $invokable->getCallable();
            } catch (Exception $e)
            {

            }
            $this->expectsException(function () use ($e)
            {
                throw $e;
            }, Exception::class, null, Exception::FAILED_RUNNING_OPERATION);

            $this->expectsException(function () use ($e)
            {
                throw $e->getPrevious();
            }, Exception::class, null, Exception::REFERENCED_SERVICE_IS_NOT_REGISTERED);

        }

        public function testNotInvokableReferencedServiceException()
        {
            $service = new DynamicMethodProvider();
            $servicesFactory = $this->getMock(ServicesFactory::class);
            $servicesFactory->expects($this->once())->method('has')->with('service.id')->willReturn(true);
            $servicesFactory->expects($this->once())->method('get')->with('service.id')->willReturn($service);

            $application = $this->getMock(ApplicationInterface::class);
            $application->method('getServicesFactory')->willReturn($servicesFactory);

            $invokable = new Invokable(new ServiceReference('service.id'));
            $invokable->setApplication($application);

            try
            {
                $invokable->getCallable();
            } catch (Exception $e)
            {

            }

            $this->expectsException(function () use ($e)
            {
                throw $e;
            }, Exception::class, null, Exception::FAILED_RUNNING_OPERATION);

            $this->expectsException(function () use ($e)
            {
                throw $e->getPrevious();
            }, Exception::class, null, Exception::REFERENCED_SERVICE_IS_NOT_CALLABLE);

        }
    }


    class InvokableClass
    {
        public function __invoke()
        {

        }
    }

    class StaticMethodProvider
    {
        public static function method() {}
    }

    class DynamicMethodProvider
    {
        public function method() {}
    }
