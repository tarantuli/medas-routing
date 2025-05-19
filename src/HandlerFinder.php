<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\ServiceManager\Cache\CacheManager;

#[Service]
readonly class HandlerFinder
{
    public function __construct(
        #[ConfigValue(ConfigOptions\GlobalPrefixOption::class)]
        private string|null  $globalPrefix,
        private CacheManager $cacheManager,
    )
    {
    }

    public function findActualHandlers(): array
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

    /** @param RouteHandler[] $handlers */
    private function selectByPriority(array $handlers): RouteHandler
    {
        // Sort by priority, then select the handler with the highest value as the actual handler
        usort($handlers, fn(RouteHandler $a, RouteHandler $b) => $a->priority() <=> $b->priority());

        return array_pop($handlers);
    }

    /** @return RouteHandler[] */
    public function getAll(): array
    {
        return $this->cacheManager->get()->get(
            [$this::class, 'getAllHandlers'],
            fn() => $this->findAll()
        );
    }

    /** @return RouteHandler[] */
    private function findAll(): array
    {
        $handlers = [];

        foreach (sm()->getServiceClassNames() as $className) {
            $class = new \ReflectionClass($className);

            $this->checkForRoutedHandlers($class, $handlers);
        }

        return $handlers;
    }

    private function checkForRoutedHandlers(\ReflectionClass $class, array &$handlers): void
    {
        if ($class->isAbstract()) {
            return;
        }

        if (!$baseRoute = attribute(Route::class, $class)) {
            return;
        }

        $routePriority = attribute(Route\Priority::class, $class);

        foreach ($class->getMethods() as $method) {
            $this->processMethod($method, $routePriority, $baseRoute, $class, $handlers);
        }
    }

    private function processMethod(
        \ReflectionMethod   $method,
        Route\Priority|null $routePriority,
        Route               $baseRoute,
        \ReflectionClass    $class,
        array               &$handlers
    ): void
    {
        if (!$baseMethod = attribute(Methods\Method::class, $method)) {
            return;
        }

        $methodPriority = attribute(Route\Priority::class, $method);
        $priority = $this->determinePriority($routePriority, $methodPriority);

        $handler = new RouteHandler(
            $this->globalPrefix,
            $baseRoute,
            $baseMethod,
            $class->name,
            $method->name,
            $priority
        );

        $handlers[] = $handler;
    }

    private function determinePriority(Route\Priority|null $routePriority, Route\Priority|null $methodPriority): int
    {
        if ($methodPriority) {
            return $methodPriority->priority;
        }

        if ($routePriority) {
            return $routePriority->priority;
        }

        return 0;
    }
}
