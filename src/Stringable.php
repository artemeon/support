<?php

declare(strict_types=1);

namespace Artemeon\Support;

class Stringable extends \Illuminate\Support\Stringable
{
    final public function __construct(mixed $value = '')
    {
        parent::__construct((string) $value);
    }

    public function indexOf(string $needle, bool $caseSensitive = true): bool | int
    {
        return StringUtil::indexOf($this->value, $needle, $caseSensitive);
    }

    public function lastIndexOf(string $needle, bool $caseSensitive = true): bool | int
    {
        return StringUtil::lastIndexOf($this->value, $needle, $caseSensitive);
    }

    public function equals(string $value): bool
    {
        return StringUtil::equals($this->value, $value);
    }

    public function limit(mixed $limit = 100, mixed $end = '…', mixed $preserveWords = false): static
    {
        return new static(StringUtil::limit($this->value, $limit, $end, $preserveWords));
    }

    /**
     * @return array<array-key, mixed>|null
     */
    public function toArray(string $delimiter = ','): ?array
    {
        return StringUtil::toArray($this->value, $delimiter);
    }

    public function removeScriptTags(): static
    {
        return new static(StringUtil::removeScriptTags($this->value));
    }

    /**
     * @return array<array-key, mixed>
     */
    public function parseUrlString(): array
    {
        return StringUtil::parseUrlString($this->value);
    }

    public function br2nl(): static
    {
        return new static(StringUtil::br2nl($this->value));
    }

    public function isNullOrEmpty(): bool
    {
        return StringUtil::isNullOrEmpty($this->value);
    }

    public function xmlSafeString(): static
    {
        return new static(StringUtil::xmlSafeString($this->value));
    }
}
