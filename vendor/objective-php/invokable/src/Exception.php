<?php

    namespace ObjectivePHP\Invokable;

    /**
     * Class Exception
     *
     * @package ObjectivePHP\Invokable
     */
    class Exception extends \Exception
    {
        const FAILED_RUNNING_OPERATION = 1;

        const CLASS_DOES_NO_EXIST      = 100;
        const CLASS_IS_NOT_INVOKABLE   = 101;

        const REFERENCED_SERVICE_IS_NOT_REGISTERED = 200;
        const REFERENCED_SERVICE_IS_NOT_CALLABLE = 201;
        const REFERENCED_SERVICE_BUILD_ERROR = 202;
    }
