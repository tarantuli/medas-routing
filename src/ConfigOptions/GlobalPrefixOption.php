<?php

declare(strict_types=1);

namespace Medas\Routing\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class GlobalPrefixOption implements ConfigOption
{
    public function __construct(
        private RoutingGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'global-prefix';
    }

    public function description(): string
    {
        return 'The global prefix to use for all routes, defaults to nothing';
    }

    public function isValid(mixed $value): bool
    {
        return $value === null || (is_string($value) && strlen($value) >= 1);
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): null
    {
        return null;
    }
}
