<?php

declare(strict_types=1);

namespace Medas\Routing\Handlers;

use Medas\Core\Interfaces\RoutedRequestHandler;

interface Handler extends RoutedRequestHandler
{
    public function handle(string $method, string $path): mixed;

    public function routeName(): string|null;

    public function endpointName(): string;
}
