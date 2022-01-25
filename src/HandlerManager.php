<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\ServiceManager\Attributes\Service;
use Symfony\Contracts\Cache\CacheInterface;

#[Service]
class HandlerManager
{
    public function __construct(
        private CacheInterface $cache,
        private HandlerFinder  $handlerFinder,
    )
    {
    }

    public function find(string $method, string $path): Handler|null
    {
        $handlers = [];

        foreach ($this->get() as $handler) {
            if ($handler->handles($method, $path)) {
                $handlers[] = $handler;
            }
        }

        if ($handlers === []) {
            return null;
        }

        // Sort by priority, then return the handler with the highest value
        usort($handlers, fn(Handler $a, Handler $b) => $a->priority() <=> $b->priority());
        return $handlers[count($handlers) - 1];
    }

    /** @return Handler[] */
    public function get(): array
    {
        return $this->cache->get('handlers', fn() => $this->handlerFinder->find());
    }
}
