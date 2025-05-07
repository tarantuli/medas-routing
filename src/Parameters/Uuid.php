<?php

declare(strict_types=1);

namespace Medas\Routing\Parameters;

use Medas\Core\Interfaces\{Uuid as UuidType, UuidProvider};

class Uuid extends BaseParameter
{
    private const REGEX_PATTERN = '[{]?[0-9a-fA-F]{8}-([0-9a-fA-F]{4}-){3}[0-9a-fA-F]{12}[}]?';

    public function pattern(): string
    {
        return sprintf('(?<%s>' . self::REGEX_PATTERN . ')', $this->name);
    }

    public function readablePattern(): string
    {
        return $this->name === 'id' ? ':uuid' : ':' . $this->name . '-uuid';
    }

    public function isValid(mixed $value): bool
    {
        return preg_match('/^' . self::REGEX_PATTERN . '$/', $value);
    }

    public function denormalize(string $value): UuidType
    {
        return service(UuidProvider::class)->fromString($value);
    }
}
