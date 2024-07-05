<?php

declare(strict_types=1);

use Artemeon\Support\ArraySectionIterator;

it('should construct', function (int $totalItems): void {
    $iterator = new ArraySectionIterator($totalItems);

    expect($iterator->getTotalItems())->toBe($totalItems);
})->with([[500], [1000]]);

it('should overwrite total items', function (int $initialItems, int $newItems): void {
    $iterator = new ArraySectionIterator($initialItems);
    $iterator->setTotalItems($newItems);

    expect($iterator->getTotalItems())->toBe($newItems);
})->with([[500, 1000]]);

it('should set the page', function (int $totalItems, int $page, int $perPage, int $expectedStart, int $expectedEnd): void {
    $iterator = new ArraySectionIterator($totalItems);
    $iterator->setPage($page);
    $iterator->setPerPage($perPage);

    expect($iterator->getPage())->toBe($page)
        ->and($iterator->getPerPage())->toBe($perPage)
        ->and($iterator->getStart())->toBe($expectedStart)
        ->and($iterator->getEnd())->toBe($expectedEnd);
})->with([[100, 1, 15, 0, 14], [1000, 5, 20, 80, 99], [50, 10, 2, 18, 19]]);

it('should set the section', function (): void {
    $iterator = new ArraySectionIterator(1000);
    $iterator->setSection(range(1, 15));

    expect($iterator->getSection())->toBe(range(1, 15));
});

it('should validate', function (int $items, int $cursor, bool $expected): void {
    $iterator = new ArraySectionIterator($items);
    $iterator->setSection(range(1, $items));
    $iterator->setCursor($cursor);

    expect($iterator->valid())->toBe($expected);
})->with([
    [15, 5, true],
    [15, 16, true], // TODO
]);

it('should get the current item', function (int $page, int $perPage, array $section): void {
    $iterator = new ArraySectionIterator(($page + 1) * $perPage);
    $iterator->setPage($page);
    $iterator->setSection($section);

    if (!array_key_exists(0, $section) || !array_key_exists(1, $section)) {
        return;
    }

    expect($iterator->current())->toBe($section[0]);
    $iterator->next();
    expect($iterator->current())->toBe($section[1]);
})->with([
    [1, 15, range(1, 15)],
    [2, 20, range(21, 40)],
]);

it('should check if the offset exists', function (array $items, int $offset, bool $expected): void {
    $iterator = new ArraySectionIterator(count($items));
    $iterator->setSection($items);

    expect($iterator->offsetExists($offset))->toBe($expected)
        ->and(isset($iterator[$offset]))->toBe($expected);
})->with([
    [range(1, 15), 0, true],
    [range(1, 30), 29, true],
    [range(1, 20), 20, false],
]);

it('should get the offset', function (array $items, int $offset, mixed $expected): void {
    $iterator = new ArraySectionIterator(count($items));
    $iterator->setSection($items);

    expect($iterator->offsetGet($offset))->toBe($expected)
        ->and($iterator[$offset])->toBe($expected);
})->with([
    [range(1, 15), 0, 1],
    [range(1, 30), 29, 30],
    [range(1, 20), 20, null],
]);

it('should set the offset', function (array $items, ?int $offset, ?int $oldItem, ?int $newItem): void {
    $iterator = new ArraySectionIterator(count($items));
    $iterator->setSection($items);

    expect($iterator->offsetGet($offset))->toBe($oldItem);
    $iterator->offsetSet($offset, $newItem);
    expect($iterator->offsetGet($offset))->toBe($newItem);
})->with([
    [range(1, 15), 0, 1, 42],
    [range(1, 30), 2, 3, 1337],
    [range(1, 20), null, null, null],
]);

it('should unset the offset', function (array $items, int $offset): void {
    $iterator = new ArraySectionIterator(count($items));
    $iterator->setSection($items);

    expect($iterator->offsetExists($offset))->toBeTrue();
    $iterator->offsetUnset($offset);
    expect($iterator->offsetExists($offset))->toBeFalse();
})->with([
    [range(1, 15), 0],
    [range(1, 30), 10],
]);

it('should return count', function (array $items, int $expected): void {
    $iterator = new ArraySectionIterator(count($items));
    $iterator->setSection($items);

    expect($iterator->count())->toBe($expected)
        ->and(count($iterator))->toBe($expected);
})->with([
    [range(1, 15), 15],
    [range(16, 30), 15],
    [range(2, 6), 5],
]);

it('should be converted to JSON', function (array $items, int $totalItems, int $perPage, int $lastPage, int $page): void {
    $iterator = new ArraySectionIterator($totalItems);
    $iterator->setPage($page);
    $iterator->setPerPage($perPage);
    $iterator->setSection($items);

    $requiredKeys = ['lastPage', 'hasPrev', 'hasNext', 'totalEntries', 'itemsPerPage', 'page', 'entries'];

    /** @var array{
     *     lastPage: int,
     *     hasPrev: bool,
     *     hasNext: bool,
     *     totalEntries: int,
     *     itemsPerPage: int,
     *     page: int,
     *     entries: mixed,
     * } $decoded */
    $decoded = json_decode($iterator->toJson(), true, 512, JSON_THROW_ON_ERROR);

    expect($iterator->toJson())->toBeJson()
        ->and(json_encode($iterator, JSON_THROW_ON_ERROR))->toBeJson()
        ->and($decoded)->toHaveKeys($requiredKeys)
        ->and($decoded)->toHaveCamelCaseKeys()
        ->and($decoded['lastPage'])->toBe($lastPage)
        ->and($decoded['hasPrev'])->toBe($page > 1)
        ->and($decoded['hasNext'])->toBe($page < $lastPage)
        ->and($decoded['totalEntries'])->toBe($totalItems)
        ->and($decoded['itemsPerPage'])->toBe($perPage)
        ->and($decoded['page'])->toBe($page)
        ->and($decoded['entries'])->toBe($items);
})->with([
    [range(1, 15), 15, 15, 1, 1],
    [range(1, 15), 30, 15, 2, 1],
    [range(11, 15), 20, 5, 4, 3],
]);
