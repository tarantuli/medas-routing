<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Routing\Methods\Method;
use Medas\Routing\Parameters\Parameter;

class Handler
{
    private string $pattern;
    /** @var Parameter[] */
    private array $parameters;
    private array $overruledHandlers;

    public function __construct(
        private Route    $route,
        private Method   $method,
        private string   $handlerName,
        private \Closure $handler,
        private int      $priority,
    )
    {
        $this->parameters = array_merge($this->route->parameters(), $this->method->parameters());
        $this->pattern = $this->compilePattern();
    }

    private function compilePattern(): string
    {
        $pattern = '';

        foreach ($this->parameters as $parameter) {
            $pattern .= '\/' . $parameter->pattern();
        }

        return '/^' . $pattern . '$/';
    }

    public function handle(string $path): mixed
    {
        preg_match($this->pattern, $path, $match);

        $arguments = [];

        foreach ($this->parameters as $parameter) {
            if ($parameter->name()) {
                $arguments[] = $parameter->normalize($match[$parameter->name()]);
            }
        }
        $handler = $this->handler;

        return $handler(...$arguments);
    }

    public function handler(): \Closure
    {
        return $this->handler;
    }

    public function handles(string $method, string $path): bool
    {
        return $this->method->name() === $method && preg_match($this->pattern, $path);
    }

    public function priority(): int
    {
        return $this->priority;
    }

    public function overruledHandlers(): array
    {
        return $this->overruledHandlers;
    }

    public function setOverruledHandlers(array $overruledHandlers): void
    {
        $this->overruledHandlers = $overruledHandlers;
    }

    public function route(): Route
    {
        return $this->route;
    }

    public function method(): Method
    {
        return $this->method;
    }

    public function endpoint(): string
    {
        $parameters = [];

        foreach ($this->parameters as $parameter) {
            $parameters[] = $parameter->readablePattern();
        }

        return '/' . implode('/', $parameters);
    }

    public function parameters(): array
    {
        return $this->parameters;
    }

    public function handlerName(): string
    {
        return $this->handlerName;
    }
}
