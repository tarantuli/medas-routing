<?php

declare(strict_types=1);

namespace Medas\Routing\Handlers;

interface Handler
{
    public function priority(): int;

    public function handles(string $method, string $path): bool;

    public function handle(string $method, string $path): mixed;

    public function setOverruledHandlers(array $handlers): void;

    public function routeName(): string|null;

    public function endpointName(): string;
}
