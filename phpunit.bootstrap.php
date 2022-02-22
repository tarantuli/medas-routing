<?php

declare(strict_types=1);

use Medas\Cache\Cache;
use Medas\Cache\FilesystemCache;
use Medas\Console\ConsolePackage;
use Medas\RoutingTest\MockUps\MockUpPackage;

require_once __DIR__ . '/bootstrap.php';

$cache = new FilesystemCache(__DIR__ . '/var/cache');
$cache->clear();
sm()->bindService($cache, Cache::class);

sm()->addPackages([
    MockUpPackage::instance(),
    ConsolePackage::instance(),
]);
