<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\Routing\RoutingPackage;
use Medas\RoutingTest\MockUps\MockUpPackage;
use Medas\ServiceManager\ServiceManager;

require_once 'vendor/autoload.php';

$sm = ServiceManager::get();

$sm->addPackages([
    RoutingPackage::instance(),
    MockUpPackage::instance(),
    ConsolePrinterPackage::instance(),
    ConfigOptionsPackage::instance(),
    ConfigManagerPackage::instance(),
]);
