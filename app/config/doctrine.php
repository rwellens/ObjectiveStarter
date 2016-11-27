<?php
/**
 * doctrine.php
 *
 * @date        27/11/2016
 * @file        doctrine.php
 */

use ObjectivePHP\Package\Doctrine\Config\EntityManager;

return [
    new EntityManager('default', [
        'host'     => '127.0.0.1',
        'user'     => 'root',
        'password' => '',
        'dbname'   => 'ObjectiveStarter',
    ]),
];

