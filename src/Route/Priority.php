<?php

declare(strict_types=1);

namespace Medas\Routing\Route;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
readonly class Priority
{
    public function __construct(
        private int $priority,
    )
    {
    }

    public function priority(): int
    {
        return $this->priority;
    }
}
