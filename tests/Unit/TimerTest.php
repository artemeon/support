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
