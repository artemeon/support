<?php

declare(strict_types=1);

namespace Artemeon\Support;

use Artemeon\Support\Exception\InvalidJsonFormatException;
use JsonException;

/**
 * @deprecated Use {@see JSON} instead.
 */
final class JsonDecoder
{
    public static function decode(string $json, bool $assoc = true, int $flags = 0): mixed
    {
        try {
            return json_decode($json, $assoc, 512, JSON_THROW_ON_ERROR | $flags);
        } catch (JsonException $exception) {
            throw InvalidJsonFormatException::wrappingException($exception);
        }
    }

    public static function decodeSilently(string $json, bool $assoc = true, int $flags = 0): mixed
    {
        try {
            return self::decode($json, $assoc, $flags);
        } catch (InvalidJsonFormatException) {
            return null;
        }
    }

    /**
     * @phpstan-param 0|JSON_INVALID_UTF8_IGNORE $flags
     */
    public static function validate(string $json, int $flags = 0): bool
    {
        return json_validate($json, 512, $flags);
    }
}
