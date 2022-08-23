<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Routing\Handlers\Handler;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Cache\{CacheManager, Interfaces\PrimesCache};

#[Service]
class HandlerManager implements PrimesCache
{
    public function __construct(
        private readonly CacheManager  $cacheManager,
        private readonly HandlerFinder $handlerFinder,
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

    public function find(string $method, string $path): Handler|null
    {
        foreach ($this->getActualHandlers() as $handler) {
            if ($handler->handles($method, $path)) {
                return $handler;
            }
        }

        return null;
    }

    public function findByName(string $name): Handler|null
    {
        foreach ($this->getActualHandlers() as $handler) {
            if ($handler->routeName() === $name) {
                return $handler;
            }
        }

        return null;
    }

    /** @return Handler[] */
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

    /** @return Handler[] */
    public function getAll(): array
    {
        return $this->cacheManager->get()->get(
            [$this::class, 'getAllHandlers'],
            fn() => $this->handlerFinder->find()
        );
    }

    /** @param Handler[] $handlers */
    private function selectByPriority(array $handlers): Handler
    {
        // Sort by priority, then select the handler with the highest value as the actual handler
        usort($handlers, fn(Handler $a, Handler $b) => $a->priority() <=> $b->priority());
        $handler = array_pop($handlers);
        $handler->setOverruledHandlers($handlers);

        return $handler;
    }

    public function primeCache(): void
    {
        $this->cacheManager->get()->remove([$this::class, 'getAllHandlers']);
        $this->cacheManager->get()->remove([$this::class, 'getActualHandlers']);
        $this->getActualHandlers();
    }
}
