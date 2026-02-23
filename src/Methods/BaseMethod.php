<?php

declare(strict_types=1);

namespace Medas\Routing\Methods;

use Medas\Routing\Parameters\{Constant, Parameter};

abstract class BaseMethod implements Method
{
    /** @var Parameter[] */
    private array $parameters;

    public function __construct(
        string|Parameter|array       $parameters = [],
        private readonly string|null $name = null,
    )
    {
        if (is_string($parameters)) {
            $parameters = [new Constant($parameters)];
        }

        $this->parameters = is_array($parameters) ? $parameters : [$parameters];

        foreach ($this->parameters as &$parameter) {
            if (is_string($parameter)) {
                $parameter = new Constant($parameter);
            }
        }
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
