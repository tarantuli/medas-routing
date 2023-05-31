<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Core\Identifier;
use Medas\Routing\Parameters\Constant;

#[\Attribute(\Attribute::TARGET_CLASS)]
class EntityRoute extends Route
{
    public function __construct(
        string $className,
    )
    {
        $lastBackslash = strrpos($className, '\\');
        $shortName = $lastBackslash === false ? $className : substr($className, $lastBackslash + 1);
        $identifier = Identifier::fromCamelCase($shortName);

        parent::__construct(new Constant($identifier->toSnakeCase()), endpointForEntity: $className);
    }
}
