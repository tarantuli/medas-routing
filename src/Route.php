<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Core\Attributes\Service;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Route extends Service
{
    /** @var Parameters\Parameter[] */
    private array $parameters;

    public function __construct(
        Parameters\Parameter|array   $parameters = [],
        private readonly string|null $endpointForEntity = null,
    )
    {
        $this->parameters = is_array($parameters) ? $parameters : [$parameters];
    }

    public function parameters(): array
    {
        return $this->parameters;
    }

    public function endpointForEntity(): string|null
    {
        return $this->endpointForEntity;
    }
}
