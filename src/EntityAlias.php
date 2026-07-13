<?php

declare(strict_types=1);

namespace Medas\Routing;

#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class EntityAlias
{
    public function __construct(
        public string $alias,
    )
    {
    }
}
