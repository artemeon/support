<?php

namespace AGP\System\Tests\Unit\Date;

use Artemeon\Support\Date\Date;
use Artemeon\Support\Exception\InvalidTimestampFormatException;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
final class DateTest extends TestCase
{
    public function testTimezoneShifts(): void
    {
        $date = new Date('20141026000000');

        $date->setNextDay();
        self::assertEquals('20141027000000', $date->getLongTimestamp());

        $date = new Date('20141027000000');
        $date->setPreviousDay();

        self::assertEquals('20141026000000', $date->getLongTimestamp());
    }

    public function testSameDay(): void
    {
        $date = new Date();

        self::assertTrue($date->isSameDay(new Date()));

        $date->setNextDay();
        self::assertFalse($date->isSameDay(new Date()));
    }

    public function testFormat(): void
    {
        $date = new Date(2024_04_11_00_00_00);

        self::assertSame('2024-04-11', $date->format('Y-m-d'));
    }

    public function testDateParams(): void
    {
        $date = new Date(0);
        self::assertEquals(00000000000000, $date->getLongTimestamp());

        $date = new Date('0');
        self::assertEquals(00000000000000, $date->getLongTimestamp());

        $date = new Date('');
        self::assertTrue($date->getLongTimestamp() > 0);

        $date = new Date(null);
        self::assertTrue($date->getLongTimestamp() > 0);

        $date = new Date(20140310123627);
        self::assertEquals(20140310123627, $date->getLongTimestamp());

        $date = new Date('20140310123627');
        self::assertEquals(20140310123627, $date->getLongTimestamp());

        $date = new Date('');
        $date2 = new Date($date);
        self::assertEquals($date2->getLongTimestamp(), $date->getLongTimestamp());

        $date = new Date(12345678);
        self::assertEquals(19700523222118, $date->getLongTimestamp());

        $date = new Date('12345678');
        self::assertEquals(19700523222118, $date->getLongTimestamp());

        $date = new Date('12345678');
        $date2 = new Date($date);
        self::assertEquals($date2->getLongTimestamp(), $date->getLongTimestamp());
    }

    public function testNextMonth(): void
    {
        $date = new Date(20130101000000);
        $date->setNextMonth();
        self::assertEquals(20130201000000, $date->getLongTimestamp());

        $date = new Date(20130115120000);
        $date->setNextMonth();
        self::assertEquals(20130215120000, $date->getLongTimestamp());

        $date = new Date(20130131120000);
        $date->setNextMonth();
        self::assertEquals(20130228120000, $date->getLongTimestamp());

        $date = new Date(20130228120000);
        $date->setNextMonth();
        self::assertEquals(20130328120000, $date->getLongTimestamp());

        $date = new Date(20130331120000);
        $date->setNextMonth();
        self::assertEquals(20130430120000, $date->getLongTimestamp());
    }

    public function testPreviousMonth(): void
    {
        $date = new Date(20130101120000);
        $date->setPreviousMonth();
        self::assertEquals(20121201120000, $date->getLongTimestamp());

        $date = new Date(20130430120000);
        $date->setPreviousMonth();
        self::assertEquals(20130330120000, $date->getLongTimestamp());

        $date = new Date(20130331120000);
        $date->setPreviousMonth();
        self::assertEquals(20130228120000, $date->getLongTimestamp());

        $date = new Date(20130831120000);
        $date->setPreviousMonth();
        self::assertEquals(20130731120000, $date->getLongTimestamp());
    }

    public function testPreviousQuarter(): void
    {
        $date = new Date(20130101120000);
        $date->setPreviousQuarter();
        self::assertEquals(20121001120000, $date->getLongTimestamp());

        $date = new Date(20130430120000);
        $date->setPreviousQuarter();
        self::assertEquals(20130130120000, $date->getLongTimestamp());

        $date = new Date(20130331120000);
        $date->setPreviousQuarter();
        self::assertEquals(20121231120000, $date->getLongTimestamp());

        $date = new Date(20130831120000);
        $date->setPreviousQuarter();
        self::assertEquals(20130531120000, $date->getLongTimestamp());
    }

    public function testPreviousHalfYear(): void
    {
        $date = new Date(20130101120000);
        $date->setPreviousHalfYear();
        self::assertEquals(20120701120000, $date->getLongTimestamp());

        $date = new Date(20130430120000);
        $date->setPreviousHalfYear();
        self::assertEquals(20121030120000, $date->getLongTimestamp());

        $date = new Date(20130331120000);
        $date->setPreviousHalfYear();
        self::assertEquals(20120931120000, $date->getLongTimestamp());

        $date = new Date(20130831120000);
        $date->setPreviousHalfYear();
        self::assertEquals(20130231120000, $date->getLongTimestamp());
    }

    public function testNextWeek(): void
    {
        $date = new Date(20130115120000);
        $date->setNextWeek();
        self::assertEquals(20130122120000, $date->getLongTimestamp());
    }

    public function testPreviousWeek(): void
    {
        $date = new Date(20130122120000);
        $date->setPreviousWeek();
        self::assertEquals(20130115120000, $date->getLongTimestamp());
    }

    public function testNextYear(): void
    {
        $date = new Date(20130115120000);
        $date->setNextYear();
        self::assertEquals(20140115120000, $date->getLongTimestamp());

        $date = new Date(20150531120000);
        $date->setNextYear();
        self::assertEquals(20160531120000, $date->getLongTimestamp());
    }

    public function testPreviousYear(): void
    {
        $date = new Date(20130122120000);
        $date->setPreviousYear();
        self::assertEquals(20120122120000, $date->getLongTimestamp());

        $date = new Date(20150531120000);
        $date->setPreviousYear();
        self::assertEquals(20140531120000, $date->getLongTimestamp());
    }

    public function testSetEndOfDay(): void
    {
        $date = new Date(20150901133737);
        $date->setEndOfDay();
        self::assertEquals(20150901235959, $date->getLongTimestamp());
    }

    public function testSetBeginningOfDay(): void
    {
        $date = new Date(20150901133737);
        $date->setBeginningOfDay();
        self::assertEquals(20150901000000, $date->getLongTimestamp());
    }

    #[DataProvider('isGreaterProvider')]
    public function testIsGreater(int $left, int $right, bool $expect): void
    {
        self::assertSame($expect, (new Date($left))->isGreater(new Date($right)));
    }

    /**
     * @return array<int, array<int, bool|int>>
     */
    public static function isGreaterProvider(): array
    {
        return [
            [2024_06_12_00_00_00, 2024_06_12_00_00_01, false],
            [2024_06_12_00_00_00, 2024_06_12_00_00_00, false],
            [2024_06_12_00_00_00, 2024_06_11_00_00_00, true],
        ];
    }

    #[DataProvider('isLowerProvider')]
    public function testIsLower(int $left, int $right, bool $expect): void
    {
        self::assertSame($expect, (new Date($left))->isLower(new Date($right)));
    }

    /**
     * @return array<int, array<int, bool|int>>
     */
    public static function isLowerProvider(): array
    {
        return [
            [2024_06_12_00_00_00, 2024_06_12_00_00_01, true],
            [2024_06_12_00_00_00, 2024_06_12_00_00_00, false],
            [2024_06_12_00_00_00, 2024_06_11_00_00_00, false],
        ];
    }

    #[DataProvider('isEqualsProvider')]
    public function testIsEquals(int $left, int $right, bool $expect): void
    {
        self::assertSame($expect, (new Date($left))->isEquals(new Date($right)));
    }

    /**
     * @return array<int, array<int, bool|int>>
     */
    public static function isEqualsProvider(): array
    {
        return [
            [2024_06_12_00_00_00, 2024_06_12_00_00_01, false],
            [2024_06_12_00_00_00, 2024_06_12_00_00_00, true],
            [2024_06_12_00_00_00, 2024_06_11_00_00_00, false],
        ];
    }

    /**
     * @return array<int, array<int, int|string>>
     */
    public static function dateDataProvider(): array
    {
        return [
            ['20240511133730', 2024, 5, 11, 13, 37, 30],
            ['20251224180000', 2025, 12, 24, 18, 0, 0],
        ];
    }

    #[DataProvider('dateDataProvider')]
    public function testGetters(string $timestamp, int $year, int $month, int $day, int $hour, int $minutes, int $seconds): void
    {
        $date = new Date($timestamp);

        self::assertEquals($year, $date->getYear());
        self::assertEquals($month, $date->getMonth());
        self::assertEquals($day, $date->getDay());
        self::assertEquals($hour, $date->getHour());
        self::assertEquals($minutes, $date->getMinute());
        self::assertEquals($seconds, $date->getSecond());
    }

    public function testJsonSerialize(): void
    {
        $date = new Date();

        self::assertEquals(json_encode($date), json_encode($date->getLongTimestamp()));
    }

    /**
     * @return array<string, array{int | string | null, bool}>
     */
    public static function isDateValueProvider(): array
    {
        return [
            'null' => [null, false],
            'int' => [1, true],
            'alpha_string' => ['foo', false],
            'alpha_numeric_string' => ['foo123', false],
            'numeric_string' => ['123', false],
            'correct_string' => ['20250130133700', true],
        ];
    }

    #[DataProvider('isDateValueProvider')]
    public function testIsDateValue(int | string | null $input, bool $expected): void
    {
        self::assertEquals($expected, Date::isDateValue($input));
    }

    public function testToString(): void
    {
        $date = new Date();

        self::assertEquals($date->getLongTimestamp(), (string) $date);
    }

    public function testInvalidInternalDateFormat(): void
    {
        $date = new Date();
        $reflection = new ReflectionClass($date);
        $property = $reflection->getProperty('strParseFormat');
        $property->setValue($date, 'foo123');

        $this->expectException(InvalidTimestampFormatException::class);

        $date->toDateTime();
    }

    public function testCurrentTimestamp(): void
    {
        $expected = (int) date('YmdHis');
        $date = Date::getCurrentTimestamp();

        self::assertEquals($expected, $date);
    }

    public function testStartOfDay(): void
    {
        self::assertEquals((new DateTime())->modify('00:00:00')->getTimestamp(), Date::forBeginOfDay()->getTimestamp());
    }

    public function testEndOfDay(): void
    {
        self::assertEquals((new DateTime())->modify('23:59:59')->getTimestamp(), Date::forEndOfDay()->getTimestamp());
    }

    public function testGetTimeInOldStyle(): void
    {
        $date = new Date();

        self::assertEquals($date->getTimeInOldStyle(), $date->getTimestamp());
    }

    public function testDayOfWeek(): void
    {
        $expected = (int) new DateTime()->format('w');

        self::assertEquals($expected, new Date()->getIntDayOfWeek());
    }

    public function testWeekOfYear(): void
    {
        $expected = (int) new DateTime()->format('W');

        self::assertEquals($expected, new Date()->getWeekOfYear());
    }

    public function testNextSecond(): void
    {
        $expected = new DateTime()->modify('+1 second')->getTimestamp();
        $actual = new Date()->setNextSecond()->getTimestamp();

        self::assertEquals($expected, $actual);
    }

    public function testPreviousSecond(): void
    {
        $expected = new DateTime()->modify('-1 second')->getTimestamp();
        $actual = new Date()->setPreviousSecond()->getTimestamp();

        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<string, array{int, int, int, int | null, int | null, int | null}>
     */
    public static function setDateDataProvider(): array
    {
        return [
            '2025-01-30' => [2025, 1, 30, 2025, 1, 30],
            '-1-01-30' => [-1, 1, 30, null, 1, 30],
            '2025-00-30' => [2025, 0, 30, 2025, null, 30],
            '2025-13-30' => [2025, 13, 30, 2025, null, 30],
            '2025-01-00' => [2025, 1, 0, 2025, 1, null],
            '2025-01-32' => [2025, 1, 32, 2025, 1, null],
            '25-01-30' => [25, 1, 30, 2025, 1, 30],
            '5-01-30' => [5, 1, 30, 2005, 1, 30],
        ];
    }

    #[DataProvider('setDateDataProvider')]
    public function testSetDate(int $year, int $month, int $day, ?int $expectedYear, ?int $expectedMonth, ?int $expectedDay): void
    {
        $date = new Date();

        $modifiedDate = $date->setDate($year, $month, $day);

        self::assertEquals($expectedYear ?? $date->getYear(), $modifiedDate->getYear());
        self::assertEquals($expectedMonth ?? $date->getMonth(), $modifiedDate->getMonth());
        self::assertEquals($expectedDay ?? $date->getDay(), $modifiedDate->getDay());
    }

    /**
     * @return array<string, array{int, int, int, int | null, int | null, int | null}>
     */
    public static function setTimeDataProvider(): array
    {
        return [
            '13:33:37' => [13, 33, 37, 13, 33, 37],
            '-1:33:37' => [-1, 33, 37, null, 33, 37],
            '24:33:37' => [24, 33, 37, null, 33, 37],
            '13:-1:37' => [13, -1, 37, 13, null, 37],
            '13:60:37' => [13, 60, 37, 13, null, 37],
            '13:37:-1' => [13, 37, -1, 13, 37, null],
            '13:37:60' => [13, 37, 60, 13, 37, null],
        ];
    }

    #[DataProvider('setTimeDataProvider')]
    public function testSetTime(int $hour, int $minutes, int $seconds, ?int $expectedHour, ?int $expectedMinutes, ?int $expectedSeconds): void
    {
        $date = new Date();
        $modifiedDate = $date->setTime($hour, $minutes, $seconds);

        self::assertEquals($expectedHour ?? $date->getHour(), $modifiedDate->getHour());
        self::assertEquals($expectedMinutes ?? $date->getMinute(), $modifiedDate->getMinute());
        self::assertEquals($expectedSeconds ?? $date->getSecond(), $modifiedDate->getSecond());
    }

    public function testFromDateTime(): void
    {
        $dateTime = new DateTime()->setDate(2033, 2, 20)->setTime(13, 33, 37);
        $date = Date::fromDateTime($dateTime);

        self::assertEquals($dateTime->format('Y-m-d H:i:s'), $date->format('Y-m-d H:i:s'));
    }

    public function testNow(): void
    {
        $dateTime = new DateTime();
        $date = Date::now();

        self::assertEquals($dateTime->format('Y-m-d H:i:s'), $date->format('Y-m-d H:i:s'));
    }

    public function testAsRfcFormat(): void
    {
        $now = new DateTime();
        $date = new Date();

        self::assertEquals($now->format(DATE_ATOM), $date->getAsRfcFormat());
    }

    public function testDiff(): void
    {
        self::assertEquals(
            new DateTime()->diff(new DateTime()->modify('+10 days'))->days,
            new Date()->diff(new DateTime()->modify('+10 days'))->days,
        );
    }

    public function testWithPreviousDay(): void
    {
        $date = new Date()->withPreviousDay();

        self::assertEquals($date->getDay(), (int) new DateTime()->modify('-1 day')->format('d'));
    }

    public function testWithNextDay(): void
    {
        $date = new Date()->withNextDay();

        self::assertEquals($date->getDay(), (int) new DateTime()->modify('+1 day')->format('d'));
    }

    public function testWithPreviousMonth(): void
    {
        $date = new Date()->withPreviousMonth();

        self::assertEquals($date->getMonth(), Carbon::now()->subMonthsWithoutOverflow()->month);
    }

    public function testWithNextMonth(): void
    {
        $date = new Date()->withNextMonth();

        self::assertEquals($date->getMonth(), Carbon::now()->addMonthsWithoutOverflow()->month);
    }

    public function testWithPreviousYear(): void
    {
        $date = new Date()->withPreviousYear();

        self::assertEquals($date->getYear(), Carbon::now()->subYearsWithoutOverflow()->year);
    }

    public function testWithNextYear(): void
    {
        $date = new Date()->withNextYear();

        self::assertEquals($date->getYear(), Carbon::now()->addYearsWithoutOverflow()->year);
    }

    public function testSub(): void
    {
        $interval = DateInterval::createFromDateString('1 day');

        $date = new Date()->sub($interval);

        self::assertEquals($date->getDay(), Carbon::now()->sub($interval)->day);
    }

    public function testAdd(): void
    {
        $interval = DateInterval::createFromDateString('1 day');

        $date = new Date()->add($interval);

        self::assertEquals($date->getDay(), Carbon::now()->add($interval)->day);
    }
}
