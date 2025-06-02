<?php

namespace Artemeon\Support\Tests\Unit;

use Artemeon\Support\Date\Date;
use Artemeon\Support\Date\DateInterface;
use Artemeon\Support\StringUtil;
use Artemeon\Support\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class StringUtilTest extends TestCase
{
    #[DataProvider('indexOfProvider')]
    public function testIndexOf(int $expected, string $haystack, string $needle, bool $caseSensitive): void
    {
        self::assertSame($expected, StringUtil::indexOf($haystack, $needle, $caseSensitive));
    }

    /**
     * @return array{int,string,string,bool}[]
     */
    public static function indexOfProvider(): array
    {
        return [
            [0, 'foobar barfoo', 'foobar', true],
            [7, 'foobar barfoo', 'barfoo', true],
            [7, 'FOOBAR foobar barfoo', 'foobar', true],
            [0, 'FOOBAR foobar barfoo', 'foobar', false],
        ];
    }

    #[DataProvider('lastIndexOfProvider')]
    public function testLastIndexOf(int $expected, string $haystack, string $needle, bool $caseSensitive): void
    {
        self::assertSame($expected, StringUtil::lastIndexOf($haystack, $needle, $caseSensitive));
    }

    /**
     * @return array{int,string,string,bool}[]
     */
    public static function lastIndexOfProvider(): array
    {
        return [
            [7, 'foobar foobar barfoo', 'foobar', true],
            [7, 'foobar FOOBAR barfoo', 'foobar', false],
        ];
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(string $strLeft, string $strRight): void
    {
        self::assertTrue(StringUtil::equals($strLeft, $strRight));
    }

    /**
     * @return array<array{string, string}>
     */
    public static function equalsProvider(): array
    {
        return [
            ['foo', 'foo'],
            ['tèst', 'tèst'],
        ];
    }

    #[DataProvider('equalsProviderFalse')]
    public function testEqualsFalse(string $strLeft, string $strRight): void
    {
        self::assertFalse(StringUtil::equals($strLeft, $strRight));
    }

    /**
     * @return array<array{string, string}>
     */
    public static function equalsProviderFalse(): array
    {
        return [
            ['test', 'tèst'],
            ['tèst', 'tést'],
        ];
    }

    #[DataProvider('limitProvider')]
    public function testLimit(string $expected, string $input, int $limit, string $end, bool $preserveWords): void
    {
        self::assertSame($expected, StringUtil::limit($input, $limit, $end, $preserveWords));
    }

    /**
     * @return array{string,string,int,string,bool}[]
     */
    public static function limitProvider(): array
    {
        return [
            ['Lorem ipsum dol…', 'Lorem ipsum dolor sit amet.', 15, '…', false],
            ['Lorem ipsum…', 'Lorem ipsum dolor sit amet.', 15, '…', true],
            ['Lorem ipsum', 'Lorem ipsum', 15, '…', true],
        ];
    }

    public function testRemoveScriptTags(): void
    {
        self::assertSame('Hello  World', StringUtil::removeScriptTags('Hello <script>alert("Hello World");</script> World'));
    }

    #[DataProvider('br2nlProvider')]
    public function testBr2nl(string $expected, string $input): void
    {
        self::assertSame($expected, StringUtil::br2nl($input));
    }

    /**
     * @return array{string,string}[]
     */
    public static function br2nlProvider(): array
    {
        return [
            ["\n", '<br>'],
            ["\n", '<br/>'],
            ["\n", '<br />'],
        ];
    }

    public function testToDate(): void
    {
        $string = '';
        $objResult = StringUtil::toDate($string);
        self::assertNull($objResult);

        $string = '0';
        $objResult = StringUtil::toDate($string);
        self::assertInstanceOf(DateInterface::class, $objResult);

        $string = new Date();
        $objResult = StringUtil::toDate($string);
        self::assertInstanceOf(DateInterface::class, $objResult);
    }

    public function testToInt(): void
    {
        $strString = '';
        $intResult = StringUtil::toInt($strString);
        self::assertNull($intResult);

        $strString = ' ';
        $intResult = StringUtil::toInt($strString);
        self::assertNull($intResult);

        $strString = 0;
        $intResult = StringUtil::toInt($strString);
        self::assertSame(0, $intResult);

        $strString = '0';
        $intResult = StringUtil::toInt($strString);
        self::assertSame(0, $intResult);

        $strString = '-42';
        $intResult = StringUtil::toInt($strString);
        self::assertSame(-42, $intResult);
    }

    public function testToFloat(): void
    {
        $strString = '';
        $intResult = StringUtil::toFloat($strString);
        self::assertNull($intResult);

        $strString = ' ';
        $intResult = StringUtil::toFloat($strString);
        self::assertNull($intResult);

        $strString = 0;
        $intResult = StringUtil::toFloat($strString);
        self::assertSame(0.0, $intResult);

        $strString = '0';
        $intResult = StringUtil::toFloat($strString);
        self::assertSame(0.0, $intResult);

        $strString = '0.0';
        $intResult = StringUtil::toFloat($strString);
        self::assertSame(0.0, $intResult);

        $strString = '-42';
        $intResult = StringUtil::toFloat($strString);
        self::assertSame(-42.0, $intResult);

        $strString = '1.2345';
        $intResult = StringUtil::toFloat($strString);
        self::assertSame(1.2345, $intResult);

        $strString = 1.2345;
        $intResult = StringUtil::toFloat($strString);
        self::assertSame(1.2345, $intResult);

        $strString = '1.2345456';
        $intResult = StringUtil::toFloat($strString);
        self::assertSame(1.2345456, $intResult);
    }

    public function testToArray(): void
    {
        // empty string
        $strString = '';
        $arrResult = StringUtil::toArray($strString);
        self::assertNull($arrResult);

        // null value
        $strString = null;
        $arrResult = StringUtil::toArray($strString);
        self::assertNull($arrResult);

        // empty string
        $strString = 'null';
        $arrResult = StringUtil::toArray($strString) ?? [];
        self::assertCount(1, $arrResult);
        self::assertEquals('null', $arrResult[0] ?? null);

        // standard cal with string
        $strString = '1,0,3';
        $arrResult = StringUtil::toArray($strString);
        self::assertIsArray($arrResult);
        self::assertCount(3, $arrResult);

        // Empty delimiter
        $strString = '1,0,3';
        $arrResult = StringUtil::toArray($strString, '');
        self::assertNull($arrResult);

        // Delimiter "."
        $strString = '1.0.3';
        $arrResult = StringUtil::toArray($strString, '.');
        self::assertIsArray($arrResult);
        self::assertCount(3, $arrResult);

        $strString = [];
        $arrResult = StringUtil::toArray($strString);
        self::assertIsArray($arrResult);
        self::assertCount(0, $arrResult);

        $strString = [2, 3, 4];
        $arrResult = StringUtil::toArray($strString);
        self::assertIsArray($arrResult);
        self::assertCount(3, $arrResult);
        self::assertEquals(2, $arrResult[0] ?? null);
        self::assertEquals(3, $arrResult[1] ?? null);
        self::assertEquals(4, $arrResult[2] ?? null);
    }

    #[DataProvider('parseUrlStringProvider')]
    public function testParseUrlString(string $strString): void
    {
        $arrExpected = [];
        parse_str($strString, $arrExpected);
        $arrResult = StringUtil::parseUrlString($strString);
        self::assertEquals($arrExpected, $arrResult);
    }

    /**
     * @return array<array{mixed}>
     */
    public static function parseUrlStringProvider(): array
    {
        return [
            ['first=value&second=value'],
            ['first=value&second=value&redirect=' . urlencode('/#avc/katze')],
            ['first=value&second=value#acbg'],
            ['first=value&arr[]=foo+bar&arr[]=baz'],
            ['first=value&arr[2]=foo+bar&arr[3]=baz'],
            ['action=search&interest[0]=sports&interest[1]=music&sort=interest'],
            ['first[0][1]=value&first[0][2]=foo'],
            ['first[0][2]=value&first[0][1]=foo'],
            ['first[1][2]=value&first[0][1]=foo'],
            ['first[][source]=value&first[][source]=foo'],
        ];
    }

    #[DataProvider('jsSafeStringProvider')]
    public function testJsSafeString(string $strString, string $strExpect): void
    {
        self::assertSame($strExpect, StringUtil::jsSafeString($strString));
    }

    /**
     * @return array<array{string, string}>
     */
    public static function jsSafeStringProvider(): array
    {
        return [
            ['foobar', 'foobar'],
            ['foo<bar', 'foo&lt;bar'],
            ['foo"bar', 'foo\&quot;bar'],
            ['foo\'bar', 'foo\&#039;bar'],
            ['foo' . "\n" . 'bar', 'foo\nbar'],
        ];
    }

    #[DataProvider('isNullOrEmptyProvider')]
    public function testIsNullOrEmpty(mixed $value, bool $expect): void
    {
        self::assertSame($expect, StringUtil::isNullOrEmpty($value));
    }

    /**
     * @return array<array{mixed, bool}>
     */
    public static function isNullOrEmptyProvider(): array
    {
        return [
            [null, true],
            ['', true],
            [0, false],
            [1, false],
            ['0', false],
            ['1', false],
            ['foo', false],
        ];
    }

    #[DataProvider('xmlSafeStringProvider')]
    public function testXmlSafeString(string $expected, ?string $input): void
    {
        self::assertSame($expected, StringUtil::xmlSafeString($input));
    }

    /**
     * @return array{string,string|null}[]
     */
    public static function xmlSafeStringProvider(): array
    {
        return [
            ['&amp;&lt;&gt;', '&<>'],
            ['', null],
        ];
    }
}
