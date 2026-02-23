<?php

declare(strict_types=1);

namespace Medas\Routing\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup};

#[Service]
readonly class RoutingGroup implements ConfigGroup
{
    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'routing';
    }
}
