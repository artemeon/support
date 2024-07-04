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
