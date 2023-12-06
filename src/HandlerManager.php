<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Core\{Attributes\Service, Interfaces\PrimesCache, Interfaces\RoutedRequestHandlerManager};
use Medas\ServiceManager\Cache\CacheManager;

#[Service]
readonly class HandlerManager implements RoutedRequestHandlerManager, PrimesCache
{
    public function __construct(
        private CacheManager  $cacheManager,
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

    public function find(string $method, string $path): Handlers\Handler|null
    {
        foreach ($this->getActualHandlers() as $handler) {
            if ($handler->handles($method, $path)) {
                return $handler;
            }
        }

        return null;
    }

    /** @return Handlers\Handler[] */
    public function getActualHandlers(): array
    {
        return $this->cacheManager->get()->get(
            [$this::class, 'getActualHandlers'],
            fn() => $this->findActualHandlers()
        );
    }

    private function findActualHandlers(): array
    {
        $handlers = [];

        foreach ($this->collectHandlersPerEndpoint() as $handlersForEndpoint) {
            $handlers[] = $this->selectByPriority($handlersForEndpoint);
        }

        return $handlers;
    }

    private function collectHandlersPerEndpoint(): array
    {
        $handlersPerEndpoint = [];

        foreach ($this->getAll() as $handler) {
            $endpoint = $handler->endpointName();
            $handlersPerEndpoint[$endpoint][] = $handler;
        }

        return $handlersPerEndpoint;
    }

    /** @return Handlers\Handler[] */
    public function getAll(): array
    {
        return $this->cacheManager->get()->get(
            [$this::class, 'getAllHandlers'],
            fn() => $this->handlerFinder->find()
        );
    }

    /** @param Handlers\Handler[] $handlers */
    private function selectByPriority(array $handlers): Handlers\Handler
    {
        // Sort by priority, then select the handler with the highest value as the actual handler
        usort(
            $handlers,
            fn(Handlers\Handler $a, Handlers\Handler $b) => $a->priority() <=> $b->priority()
        );

        return array_pop($handlers);
    }

    public function findByName(string $name): Handlers\Handler|null
    {
        foreach ($this->getActualHandlers() as $handler) {
            if ($handler->routeName() === $name) {
                return $handler;
            }
        }

        return null;
    }

    public function primeCache(): void
    {
        $this->cacheManager->get()->remove([$this::class, 'getAllHandlers']);
        $this->cacheManager->get()->remove([$this::class, 'getActualHandlers']);
        $this->getActualHandlers();
    }
}
