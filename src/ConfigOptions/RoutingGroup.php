<?php

declare(strict_types=1);

namespace Medas\Routing\ConfigOptions;

use Medas\ConfigOptions\{ConfigGroup};
use Medas\ServiceManager\AsSingleton;

class RoutingGroup implements ConfigGroup
{
    use AsSingleton;

    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'medas-routing';
    }
}
