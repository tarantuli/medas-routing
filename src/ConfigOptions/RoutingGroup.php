<?php

declare(strict_types=1);

namespace Medas\Routing\ConfigOptions;

use Medas\Core\AsSingleton;
use Medas\Core\Interfaces\ConfigGroup;

class RoutingGroup implements ConfigGroup
{
    use AsSingleton;

    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'routing';
    }
}
