<?php

namespace AGP\System\Tests\Unit\Date;

use Artemeon\Support\Date\Date;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DateTest extends TestCase
{
    public function testTimezoneShifts(): void
    {
        $objDate = new Date('20141026000000');

        $objDate->setNextDay();
        self::assertEquals($objDate->getLongTimestamp(), '20141027000000');

        $objDate = new Date('20141027000000');
        $objDate->setPreviousDay();

        self::assertEquals($objDate->getLongTimestamp(), '20141026000000');
    }

    public function testSameDay(): void
    {
        $objDate = new Date();

        self::assertTrue($objDate->isSameDay(new Date()));

        $objDate->setNextDay();
        self::assertTrue(!$objDate->isSameDay(new Date()));
    }

    public function testFormat(): void
    {
        $date = new Date(2024_04_11_00_00_00);

        self::assertSame('2024-04-11', $date->format('Y-m-d'));
    }

    public function testDateParams(): void
    {
        $objDate = new Date(0);
        self::assertEquals($objDate->getLongTimestamp(), 00000000000000);

        $objDate = new Date('0');
        self::assertEquals($objDate->getLongTimestamp(), 00000000000000);

        $objDate = new Date('');
        self::assertTrue($objDate->getLongTimestamp() > 0);

        $objDate = new Date(null);
        self::assertTrue($objDate->getLongTimestamp() > 0);

        $objDate = new Date(20140310123627);
        self::assertEquals($objDate->getLongTimestamp(), 20140310123627);

        $objDate = new Date('20140310123627');
        self::assertEquals($objDate->getLongTimestamp(), 20140310123627);

        $objDate = new Date('');
        $objDate2 = new Date($objDate);
        self::assertEquals($objDate2->getLongTimestamp(), $objDate->getLongTimestamp());

        $objDate = new Date(12345678);
        self::assertEquals($objDate->getLongTimestamp(), 19700523222118);

        $objDate = new Date('12345678');
        self::assertEquals($objDate->getLongTimestamp(), 19700523222118);

        $objDate = new Date('12345678');
        $objDate2 = new Date($objDate);
        self::assertEquals($objDate2->getLongTimestamp(), $objDate->getLongTimestamp());
    }

    public function testNextMonth(): void
    {
        $objDate = new Date(20130101000000);
        $objDate->setNextMonth();
        self::assertEquals($objDate->getLongTimestamp(), 20130201000000);

        $objDate = new Date(20130115120000);
        $objDate->setNextMonth();
        self::assertEquals($objDate->getLongTimestamp(), 20130215120000);

        $objDate = new Date(20130131120000);
        $objDate->setNextMonth();
        self::assertEquals($objDate->getLongTimestamp(), 20130228120000);

        $objDate = new Date(20130228120000);
        $objDate->setNextMonth();
        self::assertEquals($objDate->getLongTimestamp(), 20130328120000);

        $objDate = new Date(20130331120000);
        $objDate->setNextMonth();
        self::assertEquals($objDate->getLongTimestamp(), 20130430120000);
    }

    public function testPreviousMonth(): void
    {
        $objDate = new Date(20130101120000);
        $objDate->setPreviousMonth();
        self::assertEquals($objDate->getLongTimestamp(), 20121201120000);

        $objDate = new Date(20130430120000);
        $objDate->setPreviousMonth();
        self::assertEquals($objDate->getLongTimestamp(), 20130330120000);

        $objDate = new Date(20130331120000);
        $objDate->setPreviousMonth();
        self::assertEquals($objDate->getLongTimestamp(), 20130228120000);

        $objDate = new Date(20130831120000);
        $objDate->setPreviousMonth();
        self::assertEquals($objDate->getLongTimestamp(), 20130731120000);
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
        $objDate = new Date(20130115120000);
        $objDate->setNextWeek();
        self::assertEquals($objDate->getLongTimestamp(), 20130122120000);
    }

    public function testPreviousWeek(): void
    {
        $objDate = new Date(20130122120000);
        $objDate->setPreviousWeek();
        self::assertEquals($objDate->getLongTimestamp(), 20130115120000);
    }

    public function testNextYear(): void
    {
        $objDate = new Date(20130115120000);
        $objDate->setNextYear();
        self::assertEquals($objDate->getLongTimestamp(), 20140115120000);

        $objDate = new Date(20150531120000);
        $objDate->setNextYear();
        self::assertEquals($objDate->getLongTimestamp(), 20160531120000);
    }

    public function testPreviousYear(): void
    {
        $objDate = new Date(20130122120000);
        $objDate->setPreviousYear();
        self::assertEquals($objDate->getLongTimestamp(), 20120122120000);

        $objDate = new Date(20150531120000);
        $objDate->setPreviousYear();
        self::assertEquals($objDate->getLongTimestamp(), 20140531120000);
    }

    public function testSetEndOfDay(): void
    {
        $objDate = new Date(20150901133737);
        $objDate->setEndOfDay();
        self::assertEquals($objDate->getLongTimestamp(), 20150901235959);
    }

    public function testSetBeginningOfDay(): void
    {
        $objDate = new Date(20150901133737);
        $objDate->setBeginningOfDay();
        self::assertEquals($objDate->getLongTimestamp(), 20150901000000);
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
    final public function testGetters(string $timestamp, int $year, int $month, int $day, int $hour, int $minutes, int $seconds): void
    {
        $date = new Date($timestamp);

        self::assertEquals($year, $date->getYear());
        self::assertEquals($month, $date->getMonth());
        self::assertEquals($day, $date->getDay());
        self::assertEquals($hour, $date->getHour());
        self::assertEquals($minutes, $date->getMinute());
        self::assertEquals($seconds, $date->getSecond());
    }
}
