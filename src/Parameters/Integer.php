<?php

declare(strict_types=1);

namespace Medas\Routing\Parameters;

class Integer implements Parameter
{
    public function pattern(): string
    {
        return '\d+';
    }

    public function isValid(mixed $value): bool
    {
        return is_int($value);
    }
}
