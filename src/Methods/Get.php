<?php

declare(strict_types=1);

namespace Medas\Routing\Methods;

use Medas\Routing\Parameters\Parameter;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Get extends BaseMethod
{
    public function __construct(
        Parameter|array       $parameters = [],
        private readonly bool $isCollectionEndpoint = false,
        private readonly bool $isEntityEndpoint = false,
        readonly string|null  $name = null,
    )
    {
        parent::__construct($parameters, $name);
    }

    public function name(): string
    {
        return 'GET';
    }

    public function isCollectionEndpoint(): bool
    {
        return $this->isCollectionEndpoint;
    }

    public function isEntityEndpoint(): bool
    {
        return $this->isEntityEndpoint;
    }
}
