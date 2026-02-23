<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Core\Identifier;

#[\Attribute(\Attribute::TARGET_CLASS)]
class EntityRoute extends Route
{
    public function __construct(
        string $className,
    )
    {
        $lastBackslash = strrpos($className, '\\');
        $shortName = $lastBackslash === false ? $className : substr($className, $lastBackslash + 1);
        $identifier = new Identifier($shortName);

        parent::__construct(
            new Parameters\Constant($identifier->toKebabCase()),
            endpointForEntity: $className
        );
    }
}
