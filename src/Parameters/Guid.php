<?php

declare(strict_types=1);

namespace Medas\Routing\Parameters;

class Guid extends BaseParameter
{
    const REGEX_PATTERN = '(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)})';

    public function pattern(): string
    {
        return sprintf('(?<%s>' . self::REGEX_PATTERN . ')', $this->name);
    }

    public function readablePattern(): string
    {
        return ':' . $this->name;
    }

    public function isValid(mixed $value): bool
    {
        return preg_match('/^' . self::REGEX_PATTERN . '$/', $value);
    }

    public function normalize(string $value): string
    {
        return $value;
    }
}
