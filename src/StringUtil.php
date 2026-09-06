<?php

namespace Artemeon\Support;

use Artemeon\Support\Date\Date;
use Artemeon\Support\Date\DateInterface;
use Illuminate\Support\Str;

/**
 * Util class for processing strings.
 */
class StringUtil extends Str
{
    public static function of(mixed $string): Stringable
    {
        return new Stringable($string);
    }

    /**
     * Returns the index within the haystack of the first occurrence of the specified needle.
     * Returns false if the value is not found.
     */
    public static function indexOf(string $haystack, string $needle, bool $caseSensitive = true): bool | int
    {
        if ($caseSensitive) {
            return mb_strpos($haystack, $needle);
        }

        return mb_stripos($haystack, $needle);
    }

    /**
     * Returns the index within the haystack of the last occurrence of the specified needle.
     * Returns false if the needle is not found.
     */
    public static function lastIndexOf(?string $haystack, string $needle, bool $caseSensitive = true): false | int
    {
        if ($caseSensitive) {
            return mb_strrpos((string) $haystack, $needle);
        }

        return mb_strripos((string) $haystack, $needle);
    }

    /**
     * Returns whether two string are equal.
     */
    public static function equals(?string $left, ?string $right): bool
    {
        return strcasecmp((string) $left, (string) $right) === 0;
    }

    /**
     * Trim whitespaces (or other characters) from the beginning and end of a string.
     */
    public static function trim(mixed $value, mixed $charlist = null): string
    {
        return parent::trim((string) $value, $charlist);
    }

    public static function limit(mixed $value, mixed $limit = 100, mixed $end = '…', mixed $preserveWords = false): string
    {
        return parent::limit($value, $limit, $end, $preserveWords);
    }

    /**
     * Converts a string to an int.
     */
    public static function toInt(mixed $string): ?int
    {
        if (!is_numeric($string)) {
            return null;
        }

        return (int) $string;
    }

    /**
     * Converts a string to a float.
     */
    public static function toFloat(mixed $string): ?float
    {
        if (!is_numeric($string)) {
            return null;
        }

        return (float) $string;
    }

    /**
     * Converts a string to an array.
     *
     * If $strString is null, [null] will be returned.
     * If delimiter is not set and $string is not an array, [$string] will be returned.
     *
     * @param array<array-key, mixed> | string $string
     *
     * @return array<array-key, mixed>|null
     */
    public static function toArray(array | string | null $string, string $delimiter = ','): ?array
    {
        if ($string === null) {
            return null;
        }

        if (is_array($string)) {
            return $string;
        }

        if ($string !== '' && $delimiter !== '') {
            return explode($delimiter, $string);
        }

        return null;
    }

    /**
     * Converts a string to a Date.
     */
    public static function toDate(DateInterface | string | null $string): ?DateInterface
    {
        if ($string instanceof DateInterface) {
            return $string;
        }

        if (self::isNullOrEmpty($string)) {
            return null;
        }

        return new Date($string);
    }

    /**
     * Encodes a string, so it can be used in a HTML attribute as javascript string.
     */
    public static function jsSafeString(string | \Stringable $string): string
    {
        $jsonString = json_encode((string) $string, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if (self::substr($jsonString, 0, 1) === '"') {
            $jsonString = StringUtil::substr($jsonString, 1);
        }

        if (self::substr($jsonString, -1) === '"') {
            $jsonString = self::substr($jsonString, 0, -1);
        }

        $jsonString = addcslashes($jsonString, "'");

        return htmlspecialchars($jsonString, ENT_QUOTES | ENT_HTML401);
    }

    /**
     * Removes script tags.
     */
    public static function removeScriptTags(?string $string): string
    {
        return (string) preg_replace('~<script(.*)</script>~imUs', '', (string) $string);
    }

    /**
     * Builds an associative array out of an (urlencoded) param string.
     *
     * We dont use the parse_str on the complete string directly since the method checks the max_input_vars ini and we
     * easily reach this limit. Because of this we split up the string into specific chunks and then use the parse_str
     * method
     *
     * @return array<string, mixed>
     */
    public static function parseUrlString(string $strParams): array
    {
        $params = [];

        $parts = explode('&', $strParams);

        $grouped = [];
        $scalar = [];

        foreach ($parts as $strOneVal) {
            $arr = [];
            parse_str($strOneVal, $arr);

            $key = key($arr);
            $value = current($arr);

            if ($key === null) {
                continue;
            }

            if (is_array($value)) {
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [];
                }

                $grouped[$key][] = $strOneVal;
            } else {
                $scalar[] = $strOneVal;
            }
        }

        foreach ($grouped as $items) {
            $arr = [];
            parse_str(implode('&', $items), $arr);

            $params = array_merge_recursive($params, $arr);
        }

        foreach ($scalar as $item) {
            $arr = [];
            parse_str($item, $arr);

            $params = array_merge($params, $arr);
        }

        return $params;
    }

    /**
     * Replaces br tags with newlines.
     */
    public static function br2nl(string $string): string
    {
        /** @var string */
        return self::replace(['<br />', '<br/>', '<br>'], PHP_EOL, $string);
    }

    public static function isNullOrEmpty(mixed $value): bool
    {
        if (is_string($value)) {
            $value = self::trim($value);
        }

        return $value === null || $value === '';
    }

    /**
     * Makes a string safe for xml-outputs.
     */
    public static function xmlSafeString(?string $string): string
    {
        if ($string === null) {
            return '';
        }

        /** @var string */
        return static::replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], html_entity_decode($string, ENT_COMPAT, 'UTF-8'));
    }
}
