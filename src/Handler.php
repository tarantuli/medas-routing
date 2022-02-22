<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Routing\Methods\Method;
use Medas\Routing\Parameters\Constant;
use Medas\Routing\Parameters\Parameter;
use Opis\Closure\SerializableClosure;

class Handler
{
    private string $pattern;
    /** @var Parameter[] */
    private array $parameters;
    private bool $hasVariables;
    private array $overruledHandlers;

    public function __construct(
        private Route    $route,
        private Method   $method,
        private string   $handlerName,
        private \Closure $handler,
        private int      $priority,
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

    public function __serialize(): array
    {
        return [
            'pattern' => $this->pattern,
            'parameters' => $this->parameters,
            'hasVariables' => $this->hasVariables,
            'overruledHandlers' => $this->overruledHandlers,
            'route' => $this->route,
            'method' => $this->method,
            'handlerName' => $this->handlerName,
            'handler' => new SerializableClosure($this->handler),
            'priority' => $this->priority,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->pattern = $data['pattern'];
        $this->parameters = $data['parameters'];
        $this->hasVariables = $data['hasVariables'];
        $this->overruledHandlers = $data['overruledHandlers'];
        $this->route = $data['route'];
        $this->method = $data['method'];
        $this->handlerName = $data['handlerName'];
        $this->handler = $data['handler']->getClosure();
        $this->priority = $data['priority'];
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

    public function hasVariables(): bool
    {
        return $this->hasVariables;
    }
}
