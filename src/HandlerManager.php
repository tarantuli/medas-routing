<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Core\{Attributes\Service, Interfaces\HttpRequestHandlerManager, Interfaces\PrimesCache};

#[Service]
readonly class HandlerManager implements HttpRequestHandlerManager, PrimesCache
{
    public function __construct(
        private HandlerFinder $handlerFinder,
    )
    {
    }

    public function findAndExecute(string $method, string $path): mixed
    {
        if (!$handler = $this->find($method, $path)) {
            return null;
        }

        return $handler->handle($method, $path);
    }

    public function find(string $method, string $path): RouteHandler|null
    {
        return array_find($this->getHandlers(), fn($handler) => $handler->handles($method, $path));
    }

    public function findByName(string $name): RouteHandler|null
    {
        return array_find($this->getHandlers(), fn($handler) => $handler->routeName() === $name);
    }

    /** @return RouteHandler[] */
    public function getHandlers(): array
    {
        return cache(
            [$this::class, 'getActualHandlers'],
            fn() => $this->handlerFinder->findActualHandlers()
        );
    }

    public function primeCache(): void
    {
        $this->handlerFinder->clearCache();

        cacheUnset([$this::class, 'getActualHandlers']);

        $this->getHandlers();
    }
}
