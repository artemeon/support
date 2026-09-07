<?php

declare(strict_types=1);

namespace Artemeon\Support;

use JetBrains\PhpStorm\Language;
use JsonException;

class JSON
{
    /**
     * @param positive-int $depth
     *
     * @throws JsonException
     */
    public static function encode(mixed $value, int $flags = 0, int $depth = 512): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR | $flags, $depth);
    }

    /**
     * @param int<1, 2147483647> $depth
     *
     * @throws JsonException
     */
    public static function decode(#[Language('JSON')] string $json, ?bool $associative = null, int $depth = 512, int $flags = 0): mixed
    {
        return json_decode($json, $associative, $depth, JSON_THROW_ON_ERROR | $flags);
    }

    /**
     * @param int<1, 2147483647> $depth
     *
     * @return array<array-key, mixed>
     *
     * @throws JsonException
     */
    public static function decodeAsArray(#[Language('JSON')] string $json, int $depth = 512, int $flags = 0): array
    {
        return is_array($decoded = self::decode($json, false, $depth, $flags))
            ? $decoded
            : throw new JsonException(sprintf('Input JSON %s is expected to be an array. Got %s instead.', $json, gettype($decoded)));
    }

    /**
     * @param int<1, 2147483647> $depth
     *
     * @throws JsonException
     */
    public static function decodeAsObject(#[Language('JSON')] string $json, int $depth = 512, int $flags = 0): object
    {
        return is_object($decoded = self::decode($json, false, $depth, $flags))
            ? $decoded
            : throw new JsonException(sprintf('Input JSON %s is expected to be an object. Got %s instead.', $json, gettype($decoded)));
    }

    /**
     * @param int<1, 2147483647> $depth
     */
    public static function decodeSilently(#[Language('JSON')] string $json, ?bool $associative = null, int $depth = 512, int $flags = 0): mixed
    {
        try {
            return self::decode($json, $associative, $depth, $flags);
        } catch (JsonException) {
            return null;
        }
    }

    /**
     * @param int<1, 2147483647> $depth
     * @param int-mask-of<0|1048576> $flags
     */
    public static function validate(#[Language('JSON')] string $json, int $depth = 512, int $flags = 0): bool
    {
        return json_validate($json, $depth, $flags);
    }
}
