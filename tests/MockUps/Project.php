<?php

declare(strict_types=1);

namespace Medas\RoutingTest\MockUps;

class Project
{
    public function __construct(
        private readonly int $id
    )
    {
    }
}
