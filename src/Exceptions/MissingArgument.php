<?php

declare(strict_types=1);

namespace Medas\Routing\Exceptions;

use Medas\Core\Exceptions\BaseException;

class MissingArgument extends BaseException
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function pattern(): string
    {
        return 'missing argument named %s';
    }
}
