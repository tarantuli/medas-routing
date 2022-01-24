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
        foreach ($this->get() as $handler) {
            if ($handler->handles($method, $path)) {
                return $handler;
            }
        }

        return null;
    }

    /** @return Handler[] */
    public function get(): array
    {
        return $this->cache->get('handlers', fn() => $this->handlerFinder->find());
    }
}
