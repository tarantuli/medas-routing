<?php

declare(strict_types=1);

namespace Medas\RoutingTest\MockUps;

use Medas\Routing\Handlers\Handler;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class DirectHandler implements Handler
{
    public function priority(): int
    {
        return -999;
    }

    public function handles(string $method, string $path): bool
    {
        return $path === '';
    }

    public function handle(string $method, string $path): string
    {
        return 'handled directly';
    }

    public function routeName(): string|null
    {
        return null;
    }

    public function endpointName(): string
    {
        return 'direct-handler';
    }
}
