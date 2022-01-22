<?php

declare(strict_types=1);

use Medas\Routing\RoutingPackage;
use Medas\ServiceManager\ServiceManager;

chdir(__DIR__);

require_once 'vendor/autoload.php';

$sm = ServiceManager::get();

$sm->addPackages([
    RoutingPackage::instance(),
]);
