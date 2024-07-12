<?php

declare(strict_types=1);

namespace Artemeon\Support\Date;

use DateInterval;
use DateTimeInterface;
use JsonSerializable;
use Stringable;

/**
 * Immutable AGP Date API.
 */
interface DateInterface extends JsonSerializable, Stringable
{
    public function add(DateInterval $interval): DateInterface;

    public function setDate(int $year, int $month, int $day): DateInterface;

    public function setTime(int $hour, int $minute, int $second = 0): DateInterface;

    public function sub(DateInterval $interval): DateInterface;

    public function diff(DateTimeInterface $targetObject, bool $absolute = false): DateInterval;

    public function format(string $format): string;

    public function getTimestamp(): int;

    public function getWeekOfYear(): int;

    public function getYear(): int;

    public function getMonth(): int;

    public function getDay(): int;

    public function getHour(): int;

    public function getMinute(): int;

    public function getSecond(): int;

    public function isGreater(DateInterface $otherDate): bool;

    public function isLower(DateInterface $otherDate): bool;

    public function isEquals(DateInterface $otherDate): bool;

    public function withPreviousDay(): DateInterface;

    public function withNextDay(): DateInterface;

    public function withPreviousMonth(): DateInterface;

    public function withNextMonth(): DateInterface;

    public function withPreviousYear(): DateInterface;

    public function withNextYear(): DateInterface;

    public static function forBeginOfDay(): DateInterface;

    public static function forEndOfDay(): DateInterface;

    public static function fromDateTime(DateTimeInterface $dateTime): DateInterface;

    public static function now(): DateInterface;
}
