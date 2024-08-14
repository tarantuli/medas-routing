<?php

declare(strict_types=1);

namespace Medas\Routing\Parameters;

class Anything extends BaseParameter
{
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
