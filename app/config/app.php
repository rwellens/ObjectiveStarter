<?php

namespace Config;

use ObjectivePHP\Application\Config\ActionNamespace;
use ObjectivePHP\Application\Config\ApplicationName;
use ObjectivePHP\Application\Config\LayoutsLocation;
use ObjectivePHP\Matcher\Matcher;
use ObjectivePHP\Package\Doctrine\Config\EntityManager;
use ObjectivePHP\ServicesFactory\Config\Service;

return [
    new ApplicationName('Project Template'),
    new ActionNamespace('Project\\Action'),
    new LayoutsLocation('app/layouts'),
    new EntityManager('default', ['entities.locations' => 'app/src/Entity'])
];