<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Routing\Methods\Method;
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

            if (!$classAttributes = $class->getAttributes(Route::class)) {
                continue;
            }

            /** @var Route $baseRoute */
            $baseRoute = $classAttributes[0]->newInstance();

            foreach ($class->getMethods() as $method) {
                if (!$methodAttributes = $method->getAttributes(Method::class, \ReflectionAttribute::IS_INSTANCEOF)) {
                    continue;
                }

                /** @var Method $baseMethod */
                $baseMethod = $methodAttributes[0]->newInstance();
                $handler = new Handler($baseRoute, $baseMethod, $method->getClosure(service($className)));
                $handlers[] = $handler;
            }
        }

        return $handlers;
    }
}
