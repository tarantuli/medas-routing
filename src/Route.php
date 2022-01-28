<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Routing\Parameters\Parameter;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Route
{
    /** @var Parameter[] */
    private array $parameters;

    public function __construct(
        Parameter|array $parameters = []
    )
    {
        $this->parameters = is_array($parameters) ? $parameters : [$parameters];
    }

    public function parameters(): array
    {
        return $this->parameters;
    }
}
