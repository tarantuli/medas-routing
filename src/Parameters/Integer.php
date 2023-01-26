<?php

declare(strict_types=1);

namespace Medas\Routing\Parameters;

class Integer extends BaseParameter
{
    public function pattern(): string
    {
        return sprintf('(?<%s>\d+)', $this->name);
    }

    public function readablePattern(): string
    {
        return ':' . $this->name;
    }

    public function isValid(mixed $value): bool
    {
        return is_int($value);
    }

    public function denormalize(string $value): int
    {
        return (int) $value;
    }
}
