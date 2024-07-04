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
}
