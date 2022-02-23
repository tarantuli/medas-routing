<?php

declare(strict_types=1);

use Medas\Cache\FilesystemCache;
use Medas\Console\ConsolePackage;
use Medas\Routing\RoutingPackage;
use Medas\RoutingTest\MockUps\MockUpPackage;
use Medas\ServiceManager\ServiceManager;

require_once 'vendor/autoload.php';

$sm = ServiceManager::get();

$cache = new FilesystemCache(__DIR__ . '/var/cache');
$cache->clear();
$sm->setCache($cache);

$sm->addPackages([
    RoutingPackage::instance(),
    MockUpPackage::instance(),
    ConsolePackage::instance(),
]);
