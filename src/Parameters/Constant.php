<?php

declare(strict_types=1);

namespace Medas\Routing\Parameters;

class Constant implements Parameter
{
    public function __construct(private readonly string $name)
    {
    }

    public function pattern(): string
    {
        return str_replace('/', '\\/', $this->name);
    }

    public function readablePattern(): string
    {
        return $this->name;
    }

    public function isValid(mixed $value): bool
    {
        return $value == $this->name;
    }

    public function name(): ?string
    {
        return null;
    }

    public function normalize(string $value): string
    {
        return $value;
    }
}
