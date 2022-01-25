<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Routing\Methods\Method;

class Handler
{
    private string $pattern;

    public function __construct(
        private Route    $route,
        private Method   $method,
        private \Closure $handler,
        private int      $priority,
    )
    {
        $this->pattern = $this->compilePattern();
    }

    private function compilePattern(): string
    {
        $pattern = '';

        foreach (array_merge($this->route->parameters(), $this->method->parameters()) as $parameter) {
            $pattern .= '\/' . $parameter->pattern();
        }

        return '/^' . $pattern . '$/';
    }

    public function handle(): mixed
    {
        return $this->handler->__invoke();
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
