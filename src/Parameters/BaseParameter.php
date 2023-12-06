<?php

declare(strict_types=1);

namespace Medas\Routing\Parameters;

abstract class BaseParameter implements Parameter
{
    public function __construct(protected string $name)
    {
    }

    public function name(): string|null
    {
        return $this->name;
    }
}
