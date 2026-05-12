<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleSplitTest extends TestCase
{
    // ─────────────────────────────────────────────────────────────────────
    // split
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: int, 3: list<string>}>
     */
    public static function splitProvider(): iterable
    {
        yield 'comma separated' => ['a,b,c,d', ',', PHP_INT_MAX, ['a', 'b', 'c', 'd']];
        yield 'positive limit' => ['a,b,c,d', ',', 2, ['a', 'b,c,d']];
        yield 'negative limit drops trailing' => ['a,b,c,d', ',', -1, ['a', 'b', 'c']];
        yield 'no separator in value' => ['hello', ',', PHP_INT_MAX, ['hello']];
        yield 'empty value' => ['', ',', PHP_INT_MAX, ['']];
        yield 'multi-char separator' => ['a::b::c', '::', PHP_INT_MAX, ['a', 'b', 'c']];
        yield 'multibyte separator' => ['a→b→c', '→', PHP_INT_MAX, ['a', 'b', 'c']];
    }

    /**
     * @param list<string> $expected
     */
    #[DataProvider('splitProvider')]
    public function testSplit(string $value, string $separator, int $limit, array $expected): void
    {
        self::assertSame($expected, Str::split($value, $separator, $limit));
    }

    public function testSplitEmptySeparatorThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::split('hello', '');
    }

    // ─────────────────────────────────────────────────────────────────────
    // splitChars
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: int, 2: list<string>}>
     */
    public static function splitCharsProvider(): iterable
    {
        yield 'single char chunks' => ['Hello', 1, ['H', 'e', 'l', 'l', 'o']];
        yield 'two char chunks' => ['abcdef', 2, ['ab', 'cd', 'ef']];
        yield 'uneven chunks' => ['abcdefg', 3, ['abc', 'def', 'g']];
        yield 'multibyte' => ['café', 1, ['c', 'a', 'f', 'é']];
        yield 'multibyte two chunks' => ['café', 2, ['ca', 'fé']];
        yield 'empty value returns empty list' => ['', 1, []];
        yield 'chunk larger than value' => ['ab', 5, ['ab']];
    }

    /**
     * @param list<string> $expected
     */
    #[DataProvider('splitCharsProvider')]
    public function testSplitChars(string $value, int $length, array $expected): void
    {
        self::assertSame($expected, Str::splitChars($value, $length));
    }

    public function testSplitCharsZeroLengthThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::splitChars('hello', 0);
    }

    public function testSplitCharsNegativeLengthThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::splitChars('hello', -1);
    }

    // ─────────────────────────────────────────────────────────────────────
    // join
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: list<string|int|float>, 1: string, 2: string}>
     */
    public static function joinProvider(): iterable
    {
        yield 'comma' => [['a', 'b', 'c'], ',', 'a,b,c'];
        yield 'empty separator' => [['a', 'b', 'c'], '', 'abc'];
        yield 'single element' => [['hello'], ',', 'hello'];
        yield 'empty list' => [[], ',', ''];
        yield 'numeric values' => [[1, 2, 3], '-', '1-2-3'];
        yield 'mixed types' => [[1.5, 'foo', 42], '|', '1.5|foo|42'];
        yield 'multibyte separator' => [['a', 'b', 'c'], '→', 'a→b→c'];
    }

    /**
     * @param list<string|int|float> $pieces
     */
    #[DataProvider('joinProvider')]
    public function testJoin(array $pieces, string $separator, string $expected): void
    {
        self::assertSame($expected, Str::join($pieces, $separator));
    }

    public function testJoinDefaultsToEmptySeparator(): void
    {
        self::assertSame('abc', Str::join(['a', 'b', 'c']));
    }

    // ─────────────────────────────────────────────────────────────────────
    // csv
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: string, 3: list<string>}>
     */
    public static function csvProvider(): iterable
    {
        yield 'basic three fields' => ['a,b,c', ',', '"', ['a', 'b', 'c']];
        yield 'quoted comma' => ['"hello, world",2', ',', '"', ['hello, world', '2']];
        yield 'custom separator' => ['a;b;c', ';', '"', ['a', 'b', 'c']];
        yield 'empty fields normalised to empty string' => ['a,,c', ',', '"', ['a', '', 'c']];
        yield 'all empty' => [',,', ',', '"', ['', '', '']];
    }

    /**
     * @param list<string> $expected
     */
    #[DataProvider('csvProvider')]
    public function testCsv(string $value, string $separator, string $enclosure, array $expected): void
    {
        self::assertSame($expected, Str::csv($value, $separator, $enclosure));
    }

    public function testCsvDefaults(): void
    {
        self::assertSame(['a', 'b', 'c'], Str::csv('a,b,c'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // scan
    // ─────────────────────────────────────────────────────────────────────

    public function testScanReturnsCapturedTokens(): void
    {
        self::assertSame(['hello', 42, 3.14], Str::scan('hello 42 3.14', '%s %d %f'));
    }

    public function testScanReturnsNullOnTotalFailure(): void
    {
        // sscanf returns int -1 (no items matched) when format prefix mismatches,
        // which Phyx normalises to null.
        self::assertNull(Str::scan('', '%d %d %d'));
    }

    public function testScanCapturesSingleToken(): void
    {
        self::assertSame([42], Str::scan('42', '%d'));
    }

    public function testScanWithLiteralPrefix(): void
    {
        self::assertSame(['x', 5], Str::scan('foo x 5', 'foo %s %d'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // parseQuery
    // ─────────────────────────────────────────────────────────────────────

    public function testParseQueryFlat(): void
    {
        self::assertSame(['a' => '1', 'b' => '2'], Str::parseQuery('a=1&b=2'));
    }

    public function testParseQueryNested(): void
    {
        self::assertSame(['list' => ['a', 'b']], Str::parseQuery('list[]=a&list[]=b'));
    }

    public function testParseQueryEmpty(): void
    {
        self::assertSame([], Str::parseQuery(''));
    }

    public function testParseQueryUrlEncoded(): void
    {
        self::assertSame(['msg' => 'hello world'], Str::parseQuery('msg=hello%20world'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // tokenize
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: list<string>}>
     */
    public static function tokenizeProvider(): iterable
    {
        yield 'whitespace tokens' => ["a b\tc\nd", " \t\n", ['a', 'b', 'c', 'd']];
        yield 'single separator' => ['a/b/c', '/', ['a', 'b', 'c']];
        yield 'collapse consecutive separators' => ['a//b//c', '/', ['a', 'b', 'c']];
        yield 'mixed separator set' => ['a,b;c|d', ',;|', ['a', 'b', 'c', 'd']];
        yield 'empty value' => ['', ',', []];
        yield 'empty separators' => ['abc', '', []];
        yield 'value is only separators' => ['///', '/', []];
        yield 'regex meta separator' => ['a.b.c', '.', ['a', 'b', 'c']];
    }

    /**
     * @param list<string> $expected
     */
    #[DataProvider('tokenizeProvider')]
    public function testTokenize(string $value, string $separators, array $expected): void
    {
        self::assertSame($expected, Str::tokenize($value, $separators));
    }
}
