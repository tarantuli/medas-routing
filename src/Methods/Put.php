<?php

declare(strict_types=1);

namespace Medas\Routing\Methods;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Put extends BaseMethod
{
    public function name(): string
    {
        return 'PUT';
    }
}
