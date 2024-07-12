<?php

declare(strict_types=1);

namespace Artemeon\Support\Date;

use Artemeon\Support\Exception\InvalidTimestampFormatException;
use DateInterval;
use DateTime;
use DateTimeInterface;
use JetBrains\PhpStorm\Deprecated;

final class Date implements DateInterface
{
    public const INT_DAY_SUNDAY = 0;

    public const INT_DAY_MONDAY = 1;

    public const INT_DAY_TUESDAY = 2;

    public const INT_DAY_WEDNESDAY = 3;

    public const INT_DAY_THURSDAY = 4;

    public const INT_DAY_FRIDAY = 5;

    public const INT_DAY_SATURDAY = 6;

    private string $strParseFormat = 'YmdHis';

    private string $longTimestamp = '';

    public const DATE_COMPARE_GREATER_THAN = 1;

    public const DATE_COMPARE_EQUALS = 0;

    public const DATE_COMPARE_LESSER_THAN = -1;

    /**
     * Creates an instance of the Date and initializes it with the current date if no value is passed.
     * If a value is passed (int, long, Date), the value is used as the timestamp set to the new date-object.
     */
    public function __construct(mixed $longInitValue = '')
    {
        if ($longInitValue instanceof Date) {
            $longInitValue = $longInitValue->getLongTimestamp();
        }

        if ($longInitValue === '0' || $longInitValue === 0) {
            $this->setLongTimestamp('00000000000000');
        } elseif ($longInitValue === null || $longInitValue === '') {
            $this->setTimeInOldStyle(time());
        } elseif (is_int($longInitValue) || is_string($longInitValue)) {
            if (strlen('' . $longInitValue) === 14) {
                $this->setLongTimestamp($longInitValue);
            } else {
                $this->setTimeInOldStyle($longInitValue);
            }
        }
    }

    public function jsonSerialize(): string
    {
        return $this->getLongTimestamp();
    }

    /**
     * Validates if the passed param is a valid date timestamp.
     */
    public static function isDateValue(int | string | null $longValue): bool
    {
        if ($longValue === null) {
            return false;
        }

        if (is_int($longValue)) {
            return true;
        }

        return strlen($longValue) === 14 && ctype_digit($longValue);
    }

    /**
     * Returns the string-based version of the long-value currently maintained.
     */
    public function __toString(): string
    {
        return $this->longTimestamp;
    }

    /**
     * Returns the current Date objects as php's DateTime instance.
     *
     * @throw InvalidTimestampFormatException
     */
    public function toDateTime(): \DateTimeInterface
    {
        $date = DateTime::createFromFormat($this->strParseFormat, $this->longTimestamp);
        if ($date === false) {
            throw new InvalidTimestampFormatException('Invalid internal date format');
        }

        return $date;
    }

    /**
     * Compares the current date against another date and evaluates if both dates reference the same day.
     */
    public function isSameDay(Date $objDateToCompare): bool
    {
        return $objDateToCompare->getDay() === $this->getDay();
    }

    public function format(string $format): string
    {
        return $this->toDateTime()->format($format);
    }

    /**
     * Generates a long-timestamp of the current time.
     */
    public static function getCurrentTimestamp(): int
    {
        return (int) date('YmdHis');
    }

    public static function forBeginOfDay(): self
    {
        $instance = new self();
        $instance->setBeginningOfDay();

        return $instance;
    }

    public static function forEndOfDay(): self
    {
        $instance = new self();
        $instance->setEndOfDay();

        return $instance;
    }

    /**
     * Allows to init the current class with an 32Bit int value representing the seconds since 1970.
     * PHPs' time() returns 32Bit ints, too.
     */
    public function setTimeInOldStyle(int | string $intTimestamp): static
    {
        // parse timestamp in order to get schema.
        $this->longTimestamp = date($this->strParseFormat, (int) $intTimestamp);

        return $this;
    }

    /**
     * Converts the current long-timestamp to an old-fashioned int-timestamp (seconds since 1970).
     */
    public function getTimeInOldStyle(): int
    {
        return (int) mktime($this->getHour(), $this->getMinute(), $this->getSecond(), $this->getMonth(), $this->getDay(), $this->getYear());
    }

    /**
     * Returns the integer-based number of the day of the week.
     * 0 is sunday whereas 6 is the saturday.
     * This leads to:
     *   0 => Sunday
     *   1 => Monday
     *   2 => Tuesday
     *   3 => Wednesday
     *   4 => Thursday
     *   5 => Friday
     *   6 => Saturday.
     */
    public function getIntDayOfWeek(): int
    {
        return (int) date('w', $this->getTimeInOldStyle());
    }

    /**
     * Returns the week number of the year.
     */
    public function getWeekOfYear(): int
    {
        return (int) date('W', $this->getTimeInOldStyle());
    }

    /**
     * Sets the current day to the previous day.
     * Includes the handling of month / year shifts.
     */
    public function setPreviousDay(): self
    {
        return $this->subtractInterval(DateInterval::createFromDateString('1 day'));
    }

    /**
     * Sets the current day to the next day.
     * Includes the handling of month / year shifts.
     */
    public function setNextDay(): self
    {
        return $this->addInterval(DateInterval::createFromDateString('1 day'));
    }

    /**
     * Shifts the current month into the future by one.
     * If the current month has 31 days, the next one only 30, the
     * logic will remain at 30.
     */
    public function setNextMonth(): static
    {
        $objSourceDate = clone $this;

        $this->setNextDay();
        $intDaysAdded = 1;
        while ($this->getIntDay() !== $objSourceDate->getIntDay()) {
            $this->setNextDay();
            $intDaysAdded++;

            // if we skip a month border, roll back until the previous months last day
            if ($intDaysAdded > 31) {
                $this->setIntDay(1);
                $this->setPreviousDay();

                // and jump out
                break;
            }
        }

        $this->setIntHour($objSourceDate->getIntHour());
        $this->setIntMin($objSourceDate->getIntMin());
        $this->setIntSec($objSourceDate->getIntSec());

        return $this;
    }

    /**
     * Shifts the current month into the past by one.
     * If the current month has 31 days, the previous one only 30, the
     * logic will remain at 30.
     */
    public function setPreviousMonth(): static
    {
        $objSourceDate = clone $this;

        $this->setPreviousDay();
        $intDaysSubtracted = 1;
        while ($this->getIntDay() !== $objSourceDate->getIntDay()) {
            $this->setPreviousDay();
            $intDaysSubtracted++;

            // if we skip a month border, roll back until the next months last day
            if ($intDaysSubtracted > 31) {
                $this->setNextMonth();
                $this->setIntDay(1);
                $this->setPreviousDay();

                // and jump out
                break;
            }
        }

        $this->setIntHour($objSourceDate->getIntHour());
        $this->setIntMin($objSourceDate->getIntMin());
        $this->setIntSec($objSourceDate->getIntSec());

        return $this;
    }

    public function setPreviousQuarter(): self
    {
        $currentDay = $this->getIntDay();
        $this->setIntDay(1);
        for ($i = 0; $i < 3; $i++) {
            $this->setPreviousMonth();
        }
        $this->setIntDay($currentDay);

        return $this;
    }

    public function setPreviousHalfYear(): self
    {
        $currentDay = $this->getIntDay();
        $this->setIntDay(1);
        for ($i = 0; $i < 6; $i++) {
            $this->setPreviousMonth();
        }
        $this->setIntDay($currentDay);

        return $this;
    }

    /**
     * Shifts the current year into the past by one.
     */
    public function setPreviousYear(): static
    {
        $intCurrentDay = $this->getIntDay();
        $this->setIntDay(1);
        for ($intI = 0; $intI < 12; $intI++) {
            $this->setPreviousMonth();
        }
        $this->setIntDay($intCurrentDay);

        return $this;
    }

    /**
     * Shifts the current year into the future by one.
     */
    public function setNextYear(): static
    {
        $intCurrentDay = $this->getIntDay();
        $this->setIntDay(1);
        for ($intI = 0; $intI < 12; $intI++) {
            $this->setNextMonth();
        }
        $this->setIntDay($intCurrentDay);

        return $this;
    }

    /**
     * Shifts the current date one week into the future, so seven days.
     */
    public function setNextWeek(): self
    {
        return $this->addInterval(DateInterval::createFromDateString('7 days'));
    }

    /**
     * Shifts the current date one week into the future, so seven days.
     */
    public function setPreviousWeek(): self
    {
        return $this->subtractInterval(DateInterval::createFromDateString('7 days'));
    }

    public function setNextSecond(): self
    {
        $this->setTimeInOldStyle($this->getTimeInOldStyle() + 1);

        return $this;
    }

    public function setPreviousSecond(): self
    {
        $this->setTimeInOldStyle($this->getTimeInOldStyle() - 1);

        return $this;
    }

    /**
     * Sets the current time to the end of the day.
     */
    public function setEndOfDay(): static
    {
        return $this->setIntHour(23)->setIntMin(59)->setIntSec(59);
    }

    /**
     * Sets the current time to the beginning of the day.
     */
    public function setBeginningOfDay(): static
    {
        return $this->setIntHour(0)->setIntMin(0)->setIntSec(0);
    }

    /**
     * Swap the year part.
     */
    public function setIntYear(int | string $intYear): static
    {
        if ($intYear < 0) {
            return $this;
        }

        if (strlen('' . $intYear) === 2) {
            $intYear = '20' . $intYear;
        }
        if (strlen('' . $intYear) === 1) {
            $intYear = '200' . $intYear;
        }

        $strYear = sprintf('%04s', $intYear);
        $this->longTimestamp = substr_replace($this->longTimestamp, $strYear, 0, 4);

        return $this;
    }

    /**
     * Swap the month part.
     */
    public function setIntMonth(int | string $intMonth): static
    {
        if ($intMonth < 1 || $intMonth > 12) {
            return $this;
        }

        $strMonth = sprintf('%02s', $intMonth);
        $this->longTimestamp = substr_replace($this->longTimestamp, $strMonth, 4, 2);

        return $this;
    }

    /**
     * Swap the day part.
     */
    public function setIntDay(int | string $intDay): static
    {
        if ($intDay < 1 || $intDay > 31) {
            return $this;
        }

        $strDay = sprintf('%02s', $intDay);
        $this->longTimestamp = substr_replace($this->longTimestamp, $strDay, 6, 2);

        return $this;
    }

    /**
     * Swap the hour part.
     */
    public function setIntHour(int | string $intHour, bool $bitForce = false): static
    {
        if (! $bitForce && ($intHour < 0 || $intHour > 23)) {
            return $this;
        }

        $strHour = sprintf('%02s', $intHour);
        $this->longTimestamp = substr_replace($this->longTimestamp, $strHour, 8, 2);

        return $this;
    }

    /**
     * Swap the minutes part.
     */
    public function setIntMin(int | string $intMin, bool $bitForce = false): static
    {
        if (! $bitForce && ($intMin < 0 || $intMin > 59)) {
            return $this;
        }

        $strMin = sprintf('%02s', $intMin);
        $this->longTimestamp = substr_replace($this->longTimestamp, $strMin, 10, 2);

        return $this;
    }

    /**
     * Swap the seconds part.
     */
    public function setIntSec(int | string $intSec, bool $bitForce = false): static
    {
        if (! $bitForce && ($intSec < 0 || $intSec > 59)) {
            return $this;
        }

        $strSec = sprintf('%02s', $intSec);
        $this->longTimestamp = substr_replace($this->longTimestamp, $strSec, 12, 2);

        return $this;
    }

    /**
     * Get the year part.
     *
     * @deprecated Use {@see self::getYear()} instead.
     */
    #[Deprecated(reason: 'Use Date::getYear() instead.', replacement: '%class%->getYear()')]
    public function getIntYear(): string
    {
        return substr($this->longTimestamp, 0, 4);
    }

    public function getYear(): int
    {
        return (int) substr($this->longTimestamp, 0, 4);
    }

    /**
     * Get the month part.
     *
     * @deprecated Use {@see self::getMonth()} instead.
     */
    #[Deprecated(reason: 'Use Date::getYear() instead.', replacement: '%class%->getYear()')]
    public function getIntMonth(): string
    {
        return substr($this->longTimestamp, 4, 2);
    }

    public function getMonth(): int
    {
        return (int) substr($this->longTimestamp, 4, 2);
    }

    /**
     * Get the day part.
     *
     * @deprecated Use {@see self::getDay()} instead.
     */
    #[Deprecated(reason: 'Use Date::getDay() instead.', replacement: '%class%->getDay()')]
    public function getIntDay(): string
    {
        return substr($this->longTimestamp, 6, 2);
    }

    public function getDay(): int
    {
        return (int) substr($this->longTimestamp, 6, 2);
    }

    /**
     * Get the hour part.
     *
     * @deprecated Use {@see self::getHour()} instead.
     */
    #[Deprecated(reason: 'Use Date::getHour() instead.', replacement: '%class%->getHour()')]
    public function getIntHour(): string
    {
        return substr($this->longTimestamp, 8, 2);
    }

    public function getHour(): int
    {
        return (int) substr($this->longTimestamp, 8, 2);
    }

    /**
     * Get the min part.
     *
     * @deprecated Use {@see self::getMinute()} instead.
     */
    #[Deprecated(reason: 'Use Date::getMinute() instead.', replacement: '%class%->getMinute()')]
    public function getIntMin(): string
    {
        return substr($this->longTimestamp, 10, 2);
    }

    public function getMinute(): int
    {
        return (int) substr($this->longTimestamp, 10, 2);
    }

    /**
     * Get the sec part.
     *
     * @deprecated Use {@see self::getSecond()} instead.
     */
    #[Deprecated(reason: 'Use Date::getSecond() instead.', replacement: '%class%->getSecond()')]
    public function getIntSec(): string
    {
        return substr($this->longTimestamp, 12, 2);
    }

    public function getSecond(): int
    {
        return (int) substr($this->longTimestamp, 12, 2);
    }

    /**
     * Get the timstamp as a long value.
     */
    public function getLongTimestamp(): string
    {
        return $this->longTimestamp;
    }

    /**
     * Set the current timestamp.
     */
    public function setLongTimestamp(int | string | null $longTimestamp): static
    {
        if (self::isDateValue($longTimestamp)) {
            $this->longTimestamp = (string) $longTimestamp;
        }

        return $this;
    }

    public static function fromDateTime(DateTimeInterface $dateTime): self
    {
        return new self($dateTime->format('YmdHis'));
    }

    public static function now(): DateInterface
    {
        return new self();
    }

    public function getAsRfcFormat(): string
    {
        return date(DateTimeInterface::ATOM, $this->getTimeInOldStyle());
    }

    /**
     * returns 0 if dates on either side are equal
     * returns 1 if the current date is greater
     * returns -1 if the other date is greater.
     */
    public function compareTo(DateInterface $otherDate): int
    {
        return $this->getTimestamp() <=> $otherDate->getTimestamp();
    }

    public function isGreater(DateInterface $otherDate): bool
    {
        return $this->compareTo($otherDate) === self::DATE_COMPARE_GREATER_THAN;
    }

    public function isLower(DateInterface $otherDate): bool
    {
        return $this->compareTo($otherDate) === self::DATE_COMPARE_LESSER_THAN;
    }

    public function isEquals(DateInterface $otherDate): bool
    {
        return $this->compareTo($otherDate) === self::DATE_COMPARE_EQUALS;
    }

    /**
     * @throws InvalidTimestampFormatException
     */
    public function addInterval(DateInterval $dateInterval): self
    {
        $dateTime = $this->toDateTime();
        $dateTime->add($dateInterval);
        $this->setTimeInOldStyle($dateTime->getTimestamp());

        return $this;
    }

    /**
     * @throws InvalidTimestampFormatException
     */
    public function subtractInterval(DateInterval $dateInterval): self
    {
        $dateTime = $this->toDateTime();
        $dateTime->sub($dateInterval);
        $this->setTimeInOldStyle($dateTime->getTimestamp());

        return $this;
    }

    public function setDate(int $year, int $month, int $day): DateInterface
    {
        $me = clone $this;
        $me->setIntYear($year);
        $me->setIntMonth($month);
        $me->setIntDay($day);
        return $me;
    }

    public function setTime(int $hour, int $minute, int $second = 0): DateInterface
    {
        $me = clone $this;
        $me->setIntHour($hour);
        $me->setIntMin($minute);
        $me->setIntSec($second);
        return $me;
    }

    public function diff(DateTimeInterface $targetObject, bool $absolute = false): DateInterval
    {
        return $this->toDateTime()->diff($targetObject, $absolute);
    }

    public function getTimestamp(): int
    {
        return $this->toDateTime()->getTimestamp();
    }

    public function withPreviousDay(): DateInterface
    {
        $me = clone $this;
        $me->setPreviousDay();
        return $me;
    }

    public function withNextDay(): DateInterface
    {
        $me = clone $this;
        $me->setNextDay();
        return $me;
    }

    public function withPreviousMonth(): DateInterface
    {
        $me = clone $this;
        $me->setPreviousMonth();
        return $me;
    }

    public function withNextMonth(): DateInterface
    {
        $me = clone $this;
        $me->setNextMonth();
        return $me;
    }

    public function withPreviousYear(): DateInterface
    {
        $me = clone $this;
        $me->setPreviousYear();
        return $me;
    }

    public function withNextYear(): DateInterface
    {
        $me = clone $this;
        $me->setNextYear();
        return $me;
    }

    public function sub(DateInterval $interval): DateInterface
    {
        $me = clone $this;
        $me->subtractInterval($interval);
        return $me;
    }

    public function add(DateInterval $interval): DateInterface
    {
        $me = clone $this;
        $me->addInterval($interval);
        return $me;
    }
}
