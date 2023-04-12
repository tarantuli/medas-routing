<?php

declare(strict_types=1);

namespace Medas\Routing\Handlers;

use Medas\Routing\Methods\Method;
use Medas\Routing\Parameters\{Constant, Integer, Parameter};
use Medas\Routing\Route;
use Medas\ServiceManager\ParameterResolving\ParameterResolveManager;
use Medas\ServiceManager\RequestHandling\GeneratesEndpoint;

class RoutedHandler implements Handler, GeneratesEndpoint
{
    private string $pattern;
    /** @var Parameter[] */
    private array $parameters;
    private bool $hasVariables;

    public function __construct(
        private readonly string|null $globalPrefix,
        private readonly Route       $route,
        private readonly Method      $method,
        private readonly string      $handlerClass,
        private readonly string      $handlerMethod,
        private readonly int         $priority,
    )
    {
        $this->compileParameters();
        $this->pattern = $this->compilePattern();
    }

    private function compileParameters(): void
    {
        $this->parameters = array_merge(
            $this->route->parameters(),
            $this->method->parameters()
        );

        if ($this->globalPrefix) {
            array_unshift($this->parameters, new Constant($this->globalPrefix));
        }

        $this->hasVariables = false;

        foreach ($this->parameters as $parameter) {
            if (!$parameter instanceof Constant) {
                $this->hasVariables = true;
                break;
            }
        }
    }

    private function compilePattern(): string
    {
        $pattern = '';

        foreach ($this->parameters as $parameter) {
            $pattern .= '\/' . $parameter->pattern();
        }

        return '/^' . $pattern . '$/';
    }

    public function handle(string $method, string $path): mixed
    {
        preg_match($this->pattern, $path, $match);

        $arguments = [];

        foreach ($this->parameters as $parameter) {
            if ($parameter->name()) {
                $arguments[$parameter->name()] = $parameter->denormalize($match[$parameter->name()]);
            }
        }

        $handler = $this->handler();
        $arguments = service(ParameterResolveManager::class)
            ->resolveMethod(new \ReflectionFunction($handler), $arguments);

        return $handler(...$arguments);
    }

    public function handler(): \Closure
    {
        return service($this->handlerClass)->{
        $this->handlerMethod
        }(...);
    }

    public function handles(string $method, string $path): bool
    {
        return $this->method->name() === $method && preg_match($this->pattern, $path);
    }

    public function priority(): int
    {
        return $this->priority;
    }

    public function route(): Route
    {
        return $this->route;
    }

    public function method(): Method
    {
        return $this->method;
    }

    public function routeName(): string|null
    {
        return $this->method->routeName();
    }

    public function endpointName(): string
    {
        return $this->method->name() . ':' . $this->endpointPattern();
    }

    public function endpointPattern(): string
    {
        $parameters = [];

        foreach ($this->parameters as $parameter) {
            $parameters[] = $parameter->readablePattern();
        }

        return '/' . implode('/', $parameters);
    }

    public function endpoint(array $arguments = []): string
    {
        $parameters = [];

        foreach ($this->parameters as $parameter) {
            if ($parameter instanceof Constant) {
                $parameters[] = $parameter->readablePattern();
            }
            elseif ($parameter instanceof Integer) {
                if (!array_key_exists($parameter->name(), $arguments)) {
                    throw new \Exception('missing argument named ' . $parameter->name());
                }

                $parameters[] = (string) $arguments[$parameter->name()];
            }
            else {
                throw new \Exception('unhandled parameter of type ' . $parameter::class);
            }
        }

        return '/' . implode('/', $parameters);
    }

    public function parameters(): array
    {
        return $this->parameters;
    }

    public function handlerName(): string
    {
        return "$this->handlerClass::$this->handlerMethod";
    }

    public function hasVariables(): bool
    {
        return $this->hasVariables;
    }
}
