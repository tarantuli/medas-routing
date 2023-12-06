<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Core\Attributes\{ConfigValue, Service};

#[Service]
class HandlerFinder
{
    private array $handlers;

    public function __construct(
        #[ConfigValue(ConfigOptions\GlobalPrefixOption::class)]
        private readonly string|null $globalPrefix,
    )
    {
    }

    /** @return Handlers\RoutedHandler[] */
    public function find(): array
    {
        $this->handlers = [];

        $this->findHandlers();

        usort(
            $this->handlers,
            fn(Handlers\Handler $a, Handlers\Handler $b) => -($a->priority() <=> $b->priority())
        );

        return $this->handlers;
    }

    private function findHandlers(): void
    {
        foreach (sm()->getServiceClassNames() as $className) {
            $class = new \ReflectionClass($className);

            $this->checkForRoutedHandlers($class);
            $this->checkForDirectHandlers($class);
        }
    }

    private function checkForRoutedHandlers(\ReflectionClass $class): void
    {
        if ($class->isAbstract()) {
            return;
        }

        if (!$baseRoute = attribute(Route::class, $class)) {
            return;
        }

        $routePriority = attribute(Route\Priority::class, $class);

        foreach ($class->getMethods() as $method) {
            $this->processMethod($method, $routePriority, $baseRoute, $class);
        }
    }

    private function processMethod(
        \ReflectionMethod   $method,
        Route\Priority|null $routePriority,
        Route               $baseRoute,
        \ReflectionClass    $class
    ): void
    {
        if (!$baseMethod = attribute(Methods\Method::class, $method)) {
            return;
        }

        $methodPriority = attribute(Route\Priority::class, $method);
        $priority = $this->determinePriority($routePriority, $methodPriority);

        $handler = new Handlers\RoutedHandler(
            $this->globalPrefix,
            $baseRoute,
            $baseMethod,
            $class->name,
            $method->name,
            $priority
        );

        $this->handlers[] = $handler;
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

    private function checkForDirectHandlers(\ReflectionClass $class): void
    {
        if ($class->name === Handlers\RoutedHandler::class) {
            return;
        }

        if (!$class->implementsInterface(Handlers\Handler::class)) {
            return;
        }

        $this->handlers[] = service($class->name);
    }
}
