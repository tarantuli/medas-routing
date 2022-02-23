<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Routing\Methods\Method;
use Medas\Routing\Route\Priority;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class HandlerFinder
{
    private array $handlers;

    /** @return Handler[] */
    public function find(): array
    {
        $this->handlers = [];

        foreach (sm()->getServiceClassNames() as $className) {
            $this->processClass($className);
        }

        return $this->handlers;
    }

    private function processClass(string $className)
    {
        $class = new \ReflectionClass($className);

        if ($class->isAbstract()) {
            return;
        }

        if (!$baseRoute = attribute(Route::class, $class)) {
            return;
        }

        $routePriority = attribute(Priority::class, $class);

        foreach ($class->getMethods() as $method) {
            $this->processMethod($method, $routePriority, $baseRoute, $className);
        }
    }

    private function processMethod(\ReflectionMethod $method, Priority|null $routePriority, Route $baseRoute, string $className): void
    {
        if (!$baseMethod = attribute(Method::class, $method)) {
            return;
        }

        $methodPriority = attribute(Priority::class, $method);

        $priority = $this->determinePriority($routePriority, $methodPriority);

        $handler = new Handler(
            $baseRoute,
            $baseMethod,
            $className,
            $method->name,
            $priority
        );

        $this->handlers[] = $handler;
    }

    private function determinePriority(?Priority $routePriority, ?Priority $methodPriority): int
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
