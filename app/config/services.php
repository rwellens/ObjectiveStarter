<?php

use ObjectivePHP\ServicesFactory\Config\Service;
use ObjectivePHP\Matcher\Matcher;
use ObjectivePHP\ServicesFactory\ServiceReference;

/**
 * Declare your services specifications here
 */

return [
    new Service([
        'id'    => 'matcher',
        'class' => Matcher::class,
    ]),
    new Service([
        'id'     => 'service.album',
        'class'  => \Project\Service\Album::class,
        'params' => [new ServiceReference('gateway.album')],
    ]),
    new Service([
        'id'     => 'gateway.album',
        'class'  => \Project\Gateway\Album::class,
        'params' => [new ServiceReference('doctrine.em.default')],
    ]),
    new Service([
        'id'     => 'doctrine.em.default',
        'class'  => Doctrine,
        'params' => [new ServiceReference('doctrine.em.default')],
    ]),
];