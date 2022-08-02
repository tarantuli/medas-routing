<?php

declare(strict_types=1);

namespace Medas\Routing\Methods;

use Medas\Routing\Parameters\Parameter;

abstract class BaseMethod implements Method
{
    /** @var Parameter[] */
    private array $parameters;

    public function __construct(
        Parameter|array              $parameters = [],
        private readonly string|null $name = null,
    )
    {
        $this->parameters = is_array($parameters) ? $parameters : [$parameters];
    }

    public function parameters(): array
    {
        return $this->parameters;
    }

    public function routeName(): string|null
    {
        return $this->name;
    }
}
