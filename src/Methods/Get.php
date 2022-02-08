<?php

declare(strict_types=1);

namespace Medas\Routing\Methods;

use Medas\Routing\Parameters\Parameter;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Get extends BaseMethod
{
    public function __construct(
        Parameter|array $parameters = [],
        private bool    $isCollectionEndpoint = false,
        private bool    $isEntityEndpoint = false,
    )
    {
        parent::__construct($parameters);
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
