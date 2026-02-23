<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Core\Attributes\Service;

#[Service]
readonly class EndpointFinder
{
    public function __construct(
        private HandlerManager $handlerManager,
    )
    {
    }

    public function forEntity(object $instance): string|null
    {
        foreach ($this->handlerManager->getHandlers() as $handler) {
            if (!($handler instanceof RouteHandler)) {
                continue;
            }

            if ($handler->route()->endpointForEntity() !== $instance::class) {
                continue;
            }

            $method = $handler->method();

            if (!$method instanceof Methods\Get || !$method->isEntityEndpoint()) {
                continue;
            }

            $endpoint = '';

            foreach ($handler->parameters() as $parameter) {
                if ($parameter instanceof Parameters\Constant) {
                    $endpoint .= '/' . $parameter->readablePattern();
                }
                elseif ($parameter instanceof Parameters\BaseParameter) {
                    $property = new \ReflectionProperty($instance, $parameter->name());
                    $endpoint .= '/' . $property->getValue($instance);
                }
            }

            return $endpoint;
        }

        return null;
    }

    public function forCollection(string $class): string|null
    {
        foreach ($this->handlerManager->getHandlers() as $handler) {
            if (!($handler instanceof RouteHandler)) {
                continue;
            }

            if ($handler->route()->endpointForEntity() !== $class) {
                continue;
            }

            $method = $handler->method();

            if (!$method instanceof Methods\Get || !$method->isCollectionEndpoint()) {
                continue;
            }

            $endpoint = '';

            foreach ($handler->parameters() as $parameter) {
                if ($parameter instanceof Parameters\Constant) {
                    $endpoint .= '/' . $parameter->readablePattern();
                }
            }

            return $endpoint;
        }

        return null;
    }
}
