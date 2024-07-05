<?php

declare(strict_types=1);

namespace Artemeon\Support;

use ArrayAccess;
use Countable;
use JsonSerializable;

/**
 * @template TValue
 * @template-extends ArrayIterator<TValue>
 * @template-implements ArrayAccess<int | string, TValue>
 */
class ArraySectionIterator extends ArrayIterator implements ArrayAccess, Countable, JsonSerializable
{
    private int $totalItems;

    private int $page = 1;

    /** @var array<int | string, TValue> */
    private array $section = [];

    public function __construct(?int $totalItems = null)
    {
        parent::__construct([]);

        $this->totalItems = $totalItems ?? 0;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function setTotalItems(int $totalItems): void
    {
        $this->totalItems = $totalItems;
    }

    public function setPage(?int $page): void
    {
        $this->page = $page ?? 1;
        $this->rewind();
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getStart(): int
    {
        $perPage = $this->getPerPage();

        return $this->page * $perPage - $perPage;
    }

    public function getEnd(): int
    {
        return $this->getPerPage() + ($this->getStart() - 1);
    }

    /**
     * @param array<TValue> $section
     */
    public function setSection(array $section): void
    {
        $this->section = $section;
    }

    /**
     * @return array<TValue>
     */
    public function getSection(): array
    {
        return $this->section;
    }

    public function valid(): bool
    {
        return $this->key() < count($this->section);
    }

    /**
     * @return TValue | null
     */
    public function current(): mixed
    {
        return $this->section[$this->key()] ?? null;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->section[$offset]);
    }

    /**
     * @param int | string | null $offset
     *
     * @return TValue | null
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->section[$offset] ?? null;
    }

    /**
     * @param TValue $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            return;
        }

        $this->section[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->section[$offset]);
    }

    public function count(): int
    {
        return count($this->section);
    }

    public function toJson(): string
    {
        return json_encode($this, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array{
     *     lastPage: int,
     *     hasPrev: bool,
     *     hasNext: bool,
     *     totalEntries: int,
     *     itemsPerPage: int,
     *     page: int,
     *     entries: array<TValue>,
     * }
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array{
     *     lastPage: int,
     *     hasPrev: bool,
     *     hasNext: bool,
     *     totalEntries: int,
     *     itemsPerPage: int,
     *     page: int,
     *     entries: array<TValue>,
     * }
     */
    public function toArray(): array
    {
        return [
            'lastPage' => $this->getTotalPages(),
            'hasPrev' => $this->getPage() > 1,
            'hasNext' => $this->getPage() < $this->getTotalPages(),
            'totalEntries' => $this->getTotalItems(),
            'itemsPerPage' => $this->getPerPage(),
            'page' => $this->getPage(),
            'entries' => iterator_to_array($this),
        ];
    }
}
