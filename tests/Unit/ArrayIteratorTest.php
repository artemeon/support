<?php

declare(strict_types=1);

use Artemeon\Support\ArrayIterator;

it('should construct', function (array $items, int $total): void {
    $iterator = new ArrayIterator($items);

    expect($iterator->getTotalItems())->toBe($total);
})->with([
    [['foo', 'bar', 'baz'], 3],
    [['Lorem', 'Ipsum', 'Dolor', 'Sit', 'Amet'], 5],
]);

it('should calculate pages', function (array $items, int $perPage, int $pages): void {
    $iterator = new ArrayIterator($items);

    $iterator->setPerPage($perPage);

    expect($iterator->getTotalPages())->toBe($pages);
})->with([
    [range(1, 15), 15, 1],
    [range(1, 16), 15, 2],
    [range(1, 30), 15, 2],
    [range(1, 31), 15, 3],
    [range(1, 15), 20, 1],
    [range(1, 16), 20, 1],
    [range(1, 20), 20, 1],
    [range(1, 21), 20, 2],
    [range(1, 40), 20, 2],
    [range(1, 42), 20, 3],
    [range(1, 42), 0, 0],
    [range(1, 42), -1, 0],
]);

it('should return the current item', function (array $items, int $cursor, mixed $expected): void {
    $iterator = new ArrayIterator($items);
    $iterator->setCursor($cursor);

    expect($iterator->current())->toBe($expected);
})->with([
    [['foo', 'bar', 'baz'], 0, 'foo'],
    [['Lorem', 'Ipsum', 'Dolor', 'Sit', 'Amet'], 0, 'Lorem'],
    [range(1, 15), 1, 2],
]);

it('should increase cursor', function (): void {
    $iterator = new ArrayIterator(['foo', 'bar', 'baz']);

    expect($iterator->key())->toBe(0);
    $iterator->next();
    expect($iterator->key())->toBe(1);
});

it('should rewind', function (): void {
    $iterator = new ArrayIterator(['foo', 'bar', 'baz']);
    expect($iterator->key())->toBe(0);
    $iterator->next();
    expect($iterator->key())->toBe(1);
    $iterator->rewind();
    expect($iterator->key())->toBe(0);
});

it('should set the cursor', function (array $items, int $target, bool $state, int $expected): void {
    $iterator = new ArrayIterator($items);
    expect($iterator->setCursor($target))->toBe($state)
        ->and($iterator->key())->toBe($expected);
})->with([
    [range(1, 15), 7, true, 7],
    [range(1, 10), 11, false, 0],
    [range(1, 15), -1, false, 0],
]);

it('should evaluate ArrayIterator to be valid', function (): void {
    $iterator = new ArrayIterator(['foo', 'bar', 'baz']);

    expect($iterator->valid())->toBeTrue();
});

it('should get items for specific page', function (array $items, int $page, int $perPage, array $expected): void {
    $iterator = new ArrayIterator($items);
    $iterator->setPerPage($perPage);

    expect($iterator->getForPage($page))->toBe($expected);
})->with([
    [range(1, 100), 1, 15, range(1, 15)],
    [range(1, 100), 2, 15, range(16, 30)],
    [range(1, 100), 3, 15, range(31, 45)],
    [range(1, 100), 7, 15, range(91, 100)],
    [range(1, 5), 2, 3, [4, 5]],
    [range(1, 50), 0, 15, range(1, 15)],
    [range(1, 50), -1, 15, range(1, 15)],
]);
