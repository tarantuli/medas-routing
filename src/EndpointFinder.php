<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Routing\Methods\Get;
use Medas\Routing\Parameters\BaseParameter;
use Medas\Routing\Parameters\Constant;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EndpointFinder
{
    public function __construct(
        private readonly HandlerManager $handlerManager,
    )
    {
    }

    public function forEntity(object $instance): string|null
    {
        foreach ($this->handlerManager->getActualHandlers() as $handler) {
            if ($handler->route()->endpointForEntity() !== $instance::class) {
                continue;
            }

            $method = $handler->method();
            if (!$method instanceof Get || !$method->isEntityEndpoint()) {
                continue;
            }

            $endpoint = '';

            foreach ($handler->parameters() as $parameter) {
                if ($parameter instanceof Constant) {
                    $endpoint .= '/' . $parameter->pattern();
                }
                elseif ($parameter instanceof BaseParameter) {
                    $endpoint .= '/' . (new \ReflectionProperty($instance, $parameter->name()))->getValue($instance);
                }
            }

            return $endpoint;
        }

        return null;
    }

    public function forCollection(string $class): string|null
    {
        foreach ($this->handlerManager->getActualHandlers() as $handler) {
            if ($handler->route()->endpointForEntity() !== $class) {
                continue;
            }

            $method = $handler->method();
            if (!$method instanceof Get || !$method->isCollectionEndpoint()) {
                continue;
            }

            $endpoint = '';

            foreach ($handler->parameters() as $parameter) {
                if ($parameter instanceof Constant) {
                    $endpoint .= '/' . $parameter->pattern();
                }
            }

            return $endpoint;
        }

        return null;
    }
}
