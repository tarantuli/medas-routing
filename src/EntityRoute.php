<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Core\IdentifierMaker;

#[\Attribute(\Attribute::TARGET_CLASS)]
class EntityRoute extends Route
{
    public function __construct(
        string $className,
    )
    {
        $lastBackslash = strrpos($className, '\\');
        $shortName = $lastBackslash === false ? $className : substr($className, $lastBackslash + 1);
        $identifier = service(IdentifierMaker::class)->fromCamelCase($shortName);

        parent::__construct(
            new Parameters\Constant($identifier->toKebabCase()),
            endpointForEntity: $className
        );
    }
}
