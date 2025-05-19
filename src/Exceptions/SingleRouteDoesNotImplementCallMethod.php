<?php

declare(strict_types=1);

namespace Medas\Routing\Exceptions;

use Medas\Core\Exceptions\BaseException;

class SingleRouteDoesNotImplementCallMethod extends BaseException
{
    public function __construct(\ReflectionClass $class)
    {
        parent::__construct($class->getName());
    }

    public function pattern(): string
    {
        return 'class %s is tagged with SingleRoute, but does not implement the __call() method';
    }
}
