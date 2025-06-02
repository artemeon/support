<?php

namespace Artemeon\Support;

use Artemeon\Support\Date\Date;
use Artemeon\Support\Date\DateInterface;
use Illuminate\Support\Str;
use Stringable;

/**
 * Util class for processing strings.
 */
class StringUtil extends Str
{
    /**
     * Returns the index within the haystack of the first occurrence of the specified needle.
     * Returns false if the value is not found.
     */
    public static function indexOf(string | Stringable $haystack, string $needle, bool $caseSensitive = true): false | int
    {
        if ($caseSensitive) {
            return mb_strpos((string) $haystack, $needle);
        }

        return mb_stripos((string) $haystack, $needle);
    }

    /**
     * Returns the index within the haystack of the last occurrence of the specified needle.
     * Returns false if the needle is not found.
     */
    public static function lastIndexOf(string | Stringable $haystack, string $needle, bool $caseSensitive = true): false | int
    {
        if ($caseSensitive) {
            return mb_strrpos((string) $haystack, $needle);
        }

        return mb_strripos((string) $haystack, $needle);
    }

    /**
     * Returns whether two string are equal.
     */
    public static function equals(string $left, string $right): bool
    {
        return strcasecmp($left, $right) === 0;
    }

    /**
     * Returns a new string that is a substring of the given string.
     */
    public static function substring(string | Stringable $string, int $index, ?int $length = null): string
    {
        if ($length === null) {
            return mb_substr((string) $string, $index);
        }

        return mb_substr((string) $string, $index, $length);
    }

    public static function trim(mixed $value, mixed $charlist = null): string
    {
        if (is_string($value) || $value instanceof Stringable) {
            return trim((string) $value);
        }

        return '';
    }

    /**
     * {@inheritDoc}
     */
    public static function limit(mixed $value, mixed $limit = 100, mixed $end = '...', mixed $preserveWords = false): string
    {
        return parent::limit($value, $limit, $end);
    }

    /**
     * Converts a string to an int.
     */
    public static function toInt(mixed $string): ?int
    {
        if (! is_numeric($string)) {
            return null;
        }

        return (int) $string;
    }

    /**
     * Converts a string to a float.
     */
    public static function toFloat(mixed $string): ?float
    {
        if (! is_numeric($string)) {
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
     * @param array<string|int>|string|null $string
     *
     * @return array<string|int>|null
     */
    public static function toArray(array | string | null $string, ?string $delimiter = ','): ?array
    {
        if (self::isNullOrEmpty($string)) {
            return null;
        }
        if (is_array($string)) {
            return $string;
        }
        if (is_string($string) && $delimiter !== null && $delimiter !== '') {
            return explode($delimiter, $string);
        }

        return null;
    }

    /**
     * Converts a string to a Date.
     */
    public static function toDate(mixed $string): ?DateInterface
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
     * Perform a global regular expression match on a given string.
     */
    public static function matches(int | string | Stringable | null $strString, string $strPattern): bool
    {
        return mb_ereg($strPattern, (string) $strString);
    }

    /**
     * Encodes a string, so it can be used in a html attribute as javascript string.
     */
    public static function jsSafeString(string | Stringable $strString): string
    {
        $strJson = json_encode((string) $strString, JSON_UNESCAPED_UNICODE);
        if ($strJson === false) {
            $strJson = '';
        }
        if (self::substring($strJson, 0, 1) === '"') {
            $strJson = StringUtil::substring($strJson, 1);
        }
        if (self::substring($strJson, -1) === '"') {
            $strJson = self::substring($strJson, 0, -1);
        }
        $strJson = addcslashes($strJson, "'");

        return htmlspecialchars($strJson, ENT_QUOTES | ENT_HTML401);
    }

    /**
     * Removes script tags.
     */
    public static function removeScriptTags(string | Stringable $string): ?string
    {
        return preg_replace('~<script(.*)</script>~imUs', '', (string) $string); // remove script tags
    }

    /**
     * Builds an associative array out of an (urlencoded) param string.
     *
     * We dont use the parse_str on the complete string directly since the method checks the max_input_vars ini and we
     * easily reach this limit. Because of this we split up the string into specific chunks and then use the parse_str
     * method
     *
     * @return array<string, string>
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

            if (is_array($value)) {
                if (! isset($grouped[$key])) {
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
        $return = StringUtil::replace(['<br />', '<br>'], PHP_EOL, $string);
        if (is_string($return)) {
            return $return;
        }

        return '';
    }

    public static function isNullOrEmpty(mixed $value): bool
    {
        if (is_string($value)) {
            $value = self::trim($value);
        }

        return $value === null || $value === '';
    }

    public static function getShortText(string $text, int $maxLength = 250): string
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }

        $pos = strpos(wordwrap($text, $maxLength), "\n");
        if ($pos !== false) {
            return substr($text, 0, $pos) . '...';
        }

        return $text;
    }
}
