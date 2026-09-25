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
    usleep(100_000);

    expect(abs($timer->getDurationInSeconds() - 100_000 / 1_000_000))->toBeLessThan(0.02);
});

it('should round the duration to microseconds', function (): void {
    $timer = new Timer();
    $reflection = new ReflectionObject($timer);
    $reflection->getProperty('start')->setValue($timer, 0.0);
    $reflection->getProperty('end')->setValue($timer, 0.0000015);

    expect($timer->getDurationInSeconds())->toBe(0.000002);
});
