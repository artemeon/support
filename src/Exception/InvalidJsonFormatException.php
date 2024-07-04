<?php

declare(strict_types=1);

namespace Artemeon\Support\Exception;

use InvalidArgumentException;
use JsonException;

final class InvalidJsonFormatException extends InvalidArgumentException
{
    public static function wrappingException(JsonException $exception): self
    {
        return new self($exception->message, 0, $exception);
    }
}
