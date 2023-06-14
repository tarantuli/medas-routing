<?php

declare(strict_types=1);

namespace Medas\Routing\Parameters;

use Medas\Core\Interfaces\Guid as GuidType;
use Medas\Core\Interfaces\GuidProvider;

class Guid extends BaseParameter
{
    const REGEX_PATTERN = '[{]?[0-9a-fA-F]{8}-([0-9a-fA-F]{4}-){3}[0-9a-fA-F]{12}[}]?';

    public function pattern(): string
    {
        return sprintf('(?<%s>' . self::REGEX_PATTERN . ')', $this->name);
    }

    public function readablePattern(): string
    {
        return $this->name === 'id' ? ':guid' : ':' . $this->name . '-guid';
    }

    public function isValid(mixed $value): bool
    {
        return preg_match('/^' . self::REGEX_PATTERN . '$/', $value);
    }

    public function denormalize(string $value): GuidType
    {
        return service(GuidProvider::class)->fromString($value);
    }
}
