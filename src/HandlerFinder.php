<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Routing\Methods\Method;
use Medas\Routing\Route\Priority;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class HandlerFinder
{
    /** @return Handler[] */
    public function find(): array
    {
        $handlers = [];

        foreach (get_declared_classes() as $className) {
            $class = new \ReflectionClass($className);

            if (!$baseRoute = attribute(Route::class, $class)) {
                continue;
            }

            $routePriority = attribute(Priority::class, $class);

            foreach ($class->getMethods() as $method) {
                if (!$baseMethod = attribute(Method::class, $method)) {
                    continue;
                }
                $methodPriority = attribute(Priority::class, $method);

                $priority = $this->determinePriority($routePriority, $methodPriority);

                $handler = new Handler($baseRoute, $baseMethod, $method->getClosure(service($className)), $priority);
                $handlers[] = $handler;
            }
        }

        return $handlers;
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
