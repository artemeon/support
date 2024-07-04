<?php

declare(strict_types=1);

namespace Artemeon\Support;

final class Timer
{
    private ?float $start = null;
    private ?float $end = null;

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
        if ($this->start === null || $this->end === null) {
            return 0;
        }

        return round(($this->end - $this->start), 6);
    }
}
