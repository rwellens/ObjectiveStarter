<?php

namespace ObjectivePHP\Events;

class Exception extends \Exception
{
    // error codes
    const EVENT_ORIGIN_IS_IMMUTABLE = 0x20;
    const EVENT_STATUS_IS_IMMUTABLE = 0x21;
    const EVENT_IS_NOT_TRIGGERED_YET = 0x22;
	const EVENT_INVALID_CALLBACK = 0x23;
    const EVENT_INVALID_CONTEXT = 0x24;
}