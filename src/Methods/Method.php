<?php

declare(strict_types=1);

namespace Medas\Routing\Methods;

use Medas\Routing\Parameters\Parameter;

interface Method
{
    public function name(): string;

    /** @return Parameter[] */
    public function parameters(): array;

    public function routeName(): string|null;
}
