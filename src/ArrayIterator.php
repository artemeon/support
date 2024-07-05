<?php

declare(strict_types=1);

namespace Artemeon\Support;

use Iterator;
use RuntimeException;

/**
 * @template TValue
 * @template-implements Iterator<int | string, TValue>
 */
class ArrayIterator implements Iterator
{
    /** @var array<int | string, TValue> */
    private array $items = [];
    private int $cursor = 0;
    private int $perPage = 15;

    /**
     * @param array<int | string, TValue> $items
     */
    public function __construct(array $items)
    {
        $this->setItems($items);
    }

    /**
     * @param array<int | string, TValue> $items
     */
    public function setItems(array $items): void
    {
        $this->items = [];
        if ($items !== []) {
            $this->items = array_values($items);
        }
    }

    public function current(): mixed
    {
        if (!array_key_exists($this->cursor, $this->items)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Invalid current iterator');
            // @codeCoverageIgnoreEnd
        }

        return $this->items[$this->cursor];
    }

    public function next(): void
    {
        $this->cursor++;
    }

    public function key(): mixed
    {
        return $this->cursor;
    }

    public function valid(): bool
    {
        return $this->cursor < count($this->items);
    }

    public function rewind(): void
    {
        $this->cursor = 0;
    }

    public function getTotalItems(): int
    {
        return count($this->items);
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setCursor(int $cursor): bool
    {
        if ($cursor < 0 || $this->getTotalItems() <= $cursor) {
            return false;
        }

        $this->cursor = $cursor;

        return true;
    }

    public function getTotalPages(): int
    {
        $perPage = $this->getPerPage();

        if ($perPage <= 0) {
            return 0;
        }

        return (int) ceil($this->getTotalItems() / $perPage);
    }

    /**
     * @return array<int | string, TValue>
     */
    public function getForPage(int $page): array
    {
        if ($page <= 0) {
            $page = 1;
        }

        $output = [];
        $perPage = $this->getPerPage();
        $start = ($page * $perPage) - $perPage;
        $end = $perPage + $start - 1;
        $totalItems = $this->getTotalItems();

        if ($end > $totalItems) {
            $end = $totalItems - 1;
        }

        for ($i = $start; $i <= $end; $i++) {
            if (!$this->setCursor($i)) {
                break;
            }
            $output[] = $this->current();
        }

        return $output;
    }
}
