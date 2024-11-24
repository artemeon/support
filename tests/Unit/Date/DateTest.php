<?php

declare(strict_types=1);

namespace AGP\System\Tests\Unit\Date;

use Artemeon\Support\Date\Date;
use JsonException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

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
        self::assertNotTrue($date->isSameDay(new Date()));
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

    /**
     * @throws JsonException
     */
    public function testJsonSerialize(): void
    {
        $date = new Date();

        self::assertSame(json_encode($date, JSON_THROW_ON_ERROR), json_encode($date->getLongTimestamp(), JSON_THROW_ON_ERROR));
    }

    public function testToString(): void
    {
        $date = new Date();

        self::assertIsString($date->__toString());
    }

    public function testNullDateCheck(): void
    {
        self::assertFalse(Date::isDateValue(null));
    }
}
