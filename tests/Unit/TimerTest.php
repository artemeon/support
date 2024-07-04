<?php

declare(strict_types=1);

use Artemeon\Support\Timer;

it('should measure execution time', function (int $seconds) {
    $timer = new Timer();
    $timer->start();
    sleep($seconds);
    $timer->end();

    expect((int) $timer->getDurationInSeconds())->toBe($seconds);
})->with([0, 1, 2]);
