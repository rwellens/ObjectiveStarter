<?php

namespace ObjectivePHP\Invokable;


use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\ServicesFactory\ServicesFactory;

class AbstractInvokable implements InvokableInterface
{

    /**
     * @var string
     */
    protected $description;

    /**
     * @var ApplicationInterface
     */
    protected $application;


    public function __invoke(...$args)
    {

        if (!method_exists($this, 'run'))
        {
            throw new Exception(sprintf('Invokable class "%s" does not implement method "run()"', get_class($this)));
        }

        return $this->run(...$args);
    }

    /**
     * @return ApplicationInterface
     */
    public function getApplication() : ApplicationInterface
    {
        return $this->application;
    }

    /**
     * @param ApplicationInterface $application
     */
    public function setApplication(ApplicationInterface $application) : InvokableInterface
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Shorthand to access ServicesFactory
     *
     * @return ServicesFactory
     */
    public function getServicesFactory() : ServicesFactory
    {
        return $this->application->getServicesFactory();
    }

    
    
    public function getDescription() : string
    {
        return 'Instance of Invokable class ' . get_class($this);
    }


}