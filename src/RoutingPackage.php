<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Cache\CachePackage;
use Medas\ServiceManager\AsSingleton;
use Medas\ServiceManager\BasePackage;

class RoutingPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            CachePackage::class,
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
