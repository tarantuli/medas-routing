<?php

declare(strict_types=1);

namespace Medas\Routing\Parameters;

use Medas\Core\Interfaces\{Uuid as UuidType, UuidProvider};

class Uuid extends BaseParameter
{
    private const string REGEX_PATTERN = '[{]?[0-9a-fA-F]{8}-([0-9a-fA-F]{4}-){3}[0-9a-fA-F]{12}[}]?';

    public function pattern(): string
    {
        return sprintf('(?<%s>' . self::REGEX_PATTERN . ')', $this->name);
    }

    public function readablePattern(): string
    {
        return in_array($this->name, ['id', 'uuid'], true) ? ':uuid' : ':' . $this->name . '-uuid';
    }

    public function isValid(mixed $value): bool
    {
        return is_string($value) && preg_match('/^' . self::REGEX_PATTERN . '$/', $value) === 1;
    }

    public function denormalize(string $value): UuidType
    {
        return service(UuidProvider::class)->fromString($value);
    }
}
