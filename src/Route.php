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
        string|Parameters\Parameter|array $parameters = [],
        private readonly string|null      $endpointForEntity = null,
        public readonly string|null       $endpointType = null,
    )
    {
        $this->parameters = is_array($parameters) ? $parameters : [$parameters];

        foreach ($this->parameters as &$parameter) {
            if (is_string($parameter)) {
                $parameter = new Parameters\Constant($parameter);
            }
        }
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
