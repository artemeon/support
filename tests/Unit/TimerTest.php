<?php

declare(strict_types=1);

use Artemeon\Support\Timer;

it('should measure execution time', function (int $microseconds): void {
    $timer = new Timer();
    $timer->start();
    usleep($microseconds);
    $timer->end();

    expect(abs($timer->getDurationInSeconds() - $microseconds / 1_000_000))->toBeLessThan(0.02);
})->with([[100_000], [150_000], [250_000]]);

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
