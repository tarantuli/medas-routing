<?php

declare(strict_types=1);

use Medas\RoutingTest\MockUps\MockUpPackage;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Contracts\Cache\CacheInterface;

require_once __DIR__ . '/bootstrap.php';

$cache = new ApcuAdapter('entity-manager');
$cache->clear();
sm()->bindService($cache, CacheInterface::class);

sm()->addPackage(MockUpPackage::instance());
