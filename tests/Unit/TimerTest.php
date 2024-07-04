<?php

declare(strict_types=1);

use Artemeon\Support\Timer;

it('should measure execution time', function (string $seconds): void {
    $timer = new Timer();
    $timer->start();
    sleep((int) $seconds);
    $timer->end();

    expect((int) $timer->getDurationInSeconds())->toBe((int) $seconds);
})->with(['0', '1', '2']);

it('should return 0 if used incorrectly', function (): void {
    $timer = new Timer();

    expect($timer->getDurationInSeconds())->toBe(0.0);
});

it('should end the timer automatically', function (): void {
    $timer = new Timer();
    $timer->start();
    sleep(1);

    expect((int) $timer->getDurationInSeconds())->toBe(1);
});
