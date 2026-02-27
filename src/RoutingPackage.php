<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Core\{AsSingleton, BasePackage};

class RoutingPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
