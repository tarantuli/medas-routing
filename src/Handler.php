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

    public function __construct(
        private Route    $route,
        private Method   $method,
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
                $arguments[] = $match[$parameter->name()];
            }
        }

        return $this->handler->__invoke(...$arguments);
    }

    public function handles(string $method, string $path): bool
    {
        return $this->method->name() === $method && preg_match($this->pattern, $path);
    }

    public function priority(): int
    {
        return $this->priority;
    }
}
