<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Routing\Methods\Method;
use Medas\Routing\Parameters\Constant;
use Medas\Routing\Parameters\Parameter;

class Handler
{
    private string $pattern;
    /** @var Parameter[] */
    private array $parameters;
    private bool $hasVariables;
    private array $overruledHandlers;

    public function __construct(
        private readonly Route  $route,
        private readonly Method $method,
        private readonly string $handlerClass,
        private readonly string $handlerMethod,
        private readonly int    $priority,
    )
    {
        $this->compileParameters();
        $this->pattern = $this->compilePattern();
    }

    private function compileParameters(): void
    {
        $this->parameters = array_merge($this->route->parameters(), $this->method->parameters());

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

    public function handle(string $path): mixed
    {
        preg_match($this->pattern, $path, $match);

        $arguments = [];

        foreach ($this->parameters as $parameter) {
            if ($parameter->name()) {
                $arguments[] = $parameter->normalize($match[$parameter->name()]);
            }
        }

        $handler = $this->handler();

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

    public function endpoint(string|null $globalPrefix): string
    {
        $parameters = [];

        if ($globalPrefix !== null) {
            $parameters[] = $globalPrefix;
        }

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
        return "$this->handlerClass::$this->handlerMethod";
    }

    public function hasVariables(): bool
    {
        return $this->hasVariables;
    }
}
