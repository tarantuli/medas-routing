<?php

declare(strict_types=1);

namespace Medas\Routing\Route;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Priority
{
    public function __construct(
        public int $priority
    )
    {
    }
}
