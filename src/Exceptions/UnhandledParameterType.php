<?php

declare(strict_types=1);

namespace Medas\Routing\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\Routing\Parameters\Parameter;

class UnhandledParameterType extends BaseException
{
    public function __construct(Parameter $parameter)
    {
        parent::__construct($parameter::class);
    }

    public function pattern(): string
    {
        return 'unhandled parameter type %s';
    }
}
