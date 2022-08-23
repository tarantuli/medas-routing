<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Routing\ConfigOptions\GlobalPrefixOption;
use Medas\Routing\Handlers\Handler;
use Medas\Routing\Handlers\RoutedHandler;
use Medas\Routing\Methods\Method;
use Medas\Routing\Route\Priority;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\ConfigOptions\ConfigValue;

#[Service]
class HandlerFinder
{
    private array $handlers;

    public function __construct(
        #[ConfigValue(GlobalPrefixOption::class)]
        private readonly string|null $globalPrefix,
    )
    {
    }

    /** @return RoutedHandler[] */
    public function find(): array
    {
        $this->handlers = [];

        $this->findHandlers();

        usort($this->handlers, fn(Handler $a, Handler $b) => -($a->priority() <=> $b->priority()));

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

        $routePriority = attribute(Priority::class, $class);

        foreach ($class->getMethods() as $method) {
            $this->processMethod($method, $routePriority, $baseRoute, $class);
        }
    }

    private function processMethod(\ReflectionMethod $method, Priority|null $routePriority, Route $baseRoute, \ReflectionClass $class): void
    {
        if (!$baseMethod = attribute(Method::class, $method)) {
            return;
        }

        $methodPriority = attribute(Priority::class, $method);

        $priority = $this->determinePriority($routePriority, $methodPriority);

        $handler = new RoutedHandler(
            $this->globalPrefix,
            $baseRoute,
            $baseMethod,
            $class->name,
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

    private function checkForDirectHandlers(\ReflectionClass $class): void
    {
        if ($class->name === RoutedHandler::class) {
            return;
        }

        if (!$class->implementsInterface(Handler::class)) {
            return;
        }

        $this->handlers[] = service($class->name);
    }
}
