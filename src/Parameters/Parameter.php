<?php

declare(strict_types=1);

namespace Medas\Routing\Parameters;

interface Parameter
{
    public function pattern(): string;

    public function isValid(mixed $value): bool;
}
