<?php

declare(strict_types=1);

use Artemeon\Support\FullText;

it('should calculate relevance', function (string $input, string $firstQuery, string $secondQuery): void {
    $firstRelevance = FullText::make($input)->search($firstQuery);
    $secondRelevance = FullText::make($input)->search($secondQuery);

    expect($firstRelevance)->toBeGreaterThan($secondRelevance);
})->with([
    ['Foo Bar Baz', 'Foo', 'az'],
    ['Lorem Ipsum Dolor Sit Amet', 'Ipsum Amet', 'orem olor'],
    ['non pariatur sunt', 'ariatur', 'aratur'],
]);

it('should return 1.0 when no query was provided', function (): void {
    expect(FullText::make('foo bar')->search(''))->toBe(1.0);
});

it('should score each match type', function (string $input, string $query, float $expected): void {
    expect(FullText::make($input)->search($query))->toBe($expected);
})->with([
    'exact' => ['foo', 'foo', 16100.0],
    'prefix' => ['foobar', 'foo', 6000.0],
    'infix' => ['barfoo', 'foo', 1000.0],
    'similar at threshold' => ['abcdy', 'abcdx', 80.0],
    'similar below threshold' => ['abcdefghijklmnopqrs22222', 'abcdefghijklmnopqrs11111', 0.0],
    'earlier tokens weigh double' => ['foo bar', 'foo bar', 48300.0],
]);
