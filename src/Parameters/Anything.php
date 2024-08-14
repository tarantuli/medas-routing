<?php

declare(strict_types=1);

namespace Medas\Routing\Parameters;

class Anything implements Parameter
{
    public function __construct(
        protected string|null $name = null,
    )
    {
    }

    public function name(): string|null
    {
        return $this->name;
    }

    public function pattern(): string
    {
        return '.*';
    }

    public function readablePattern(): string
    {
        return '*';
    }

    public function isValid(mixed $value): bool
    {
        return true;
    }

    public function denormalize(string $value): null
    {
        return null;
    }
}
