<?php

declare(strict_types=1);

namespace Artemeon\Support;

use Artemeon\Support\Exception\InvalidJsonFormatException;
use JsonException;

final class JsonDecoder
{
    public static function decode(string $json, bool $assoc = true): mixed
    {
        try {
            return json_decode($json, $assoc, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw InvalidJsonFormatException::wrappingException($exception);
        }
    }

    public static function decodeSilently(string $json, bool $assoc = true): mixed
    {
        try {
            return self::decode($json, $assoc);
        } catch (InvalidJsonFormatException) {
            return null;
        }
    }

    public static function validate(string $json): bool
    {
        return json_validate($json);
    }
}
