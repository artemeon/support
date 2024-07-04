<?php

declare(strict_types=1);

namespace Artemeon\Support;

final class Timer
{
    private float $start;
    private float $end;

    public function start(): void
    {
        $this->start = microtime(true);
    }

    public function end(): void
    {
        $this->end = microtime(true);
    }

    public function getDurationInSeconds(): float
    {
        return round(($this->end - $this->start), 6);
    }
}
