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
        foreach ($this->getHandlers() as $handler) {
            if ($handler->handles($method, $path)) {
                return $handler;
            }
        }

        return null;
    }

    public function findByName(string $name): RouteHandler|null
    {
        foreach ($this->getHandlers() as $handler) {
            if ($handler->routeName() === $name) {
                return $handler;
            }
        }

        return null;
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
        cacheUnset([HandlerFinder::class, 'getAllHandlers']);
        cacheUnset([$this::class, 'getActualHandlers']);

        $this->getHandlers();
    }
}
