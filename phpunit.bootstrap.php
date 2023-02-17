<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\Routing\RoutingPackage;
use Medas\RoutingTest\MockUps\MockUpPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        RoutingPackage::instance(),
        MockUpPackage::instance(),
        ConsolePrinterPackage::instance(),
        ConfigOptionsPackage::instance(),
        ConfigManagerPackage::instance(),
    ]);

    return $config;
});
