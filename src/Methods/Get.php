<?php

declare(strict_types=1);

namespace Medas\Routing\Methods;

use Medas\Routing\Parameters\Parameter;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Get extends BaseMethod
{
    public function __construct(
        Parameter|array     $parameters = [],
        private string|null $forCollectionsOf = null,
        private string|null $forItemsOf = null,
    )
    {
        parent::__construct($parameters);
    }

    public function name(): string
    {
        return 'GET';
    }

    public function forCollectionsOf(): ?string
    {
        return $this->forCollectionsOf;
    }

    public function forItemsOf(): ?string
    {
        return $this->forItemsOf;
    }
}
