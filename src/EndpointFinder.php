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
        private HandlerManager $handlerManager,
    )
    {
    }

    public function forItem(object $instance): string|null
    {
        foreach ($this->handlerManager->getActualHandlers() as $handler) {
            $method = $handler->method();
            if (!($method instanceof Get) || $method->forItemsOf() !== $instance::class) {
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
            $method = $handler->method();
            if (!($method instanceof Get) || $method->forCollectionsOf() !== $class) {
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
