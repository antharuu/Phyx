<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleEscapeTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function addSlashesProvider(): iterable
    {
        yield 'single quote' => ["It's", "It\\'s"];
        yield 'double quote' => ['He said "hi"', 'He said \\"hi\\"'];
        yield 'backslash' => ['a\\b', 'a\\\\b'];
        yield 'nul byte' => ["a\0b", 'a\\0b'];
        yield 'no escape needed' => ['hello', 'hello'];
        yield 'empty' => ['', ''];
    }

    #[DataProvider('addSlashesProvider')]
    public function testAddSlashes(string $input, string $expected): void
    {
        self::assertSame($expected, Str::addSlashes($input));
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function addCSlashesProvider(): iterable
    {
        yield 'uppercase range' => ['Hello', 'A..Z', '\\Hello'];
        yield 'lowercase range' => ['abc', 'a..c', '\\a\\b\\c'];
        yield 'no match' => ['xyz', 'a..c', 'xyz'];
        yield 'empty value' => ['', 'A..Z', ''];
    }

    #[DataProvider('addCSlashesProvider')]
    public function testAddCSlashes(string $input, string $charset, string $expected): void
    {
        self::assertSame($expected, Str::addCSlashes($input, $charset));
    }

    public function testStripSlashesRoundtrip(): void
    {
        $original = "It's a \"test\"";
        self::assertSame($original, Str::stripSlashes(Str::addSlashes($original)));
    }

    public function testStripSlashesBare(): void
    {
        self::assertSame('hello', Str::stripSlashes('hello'));
    }

    public function testStripSlashesEmpty(): void
    {
        self::assertSame('', Str::stripSlashes(''));
    }

    public function testStripCSlashesDecodesHexEscape(): void
    {
        // \x41 is the C-style escape for the byte 0x41 ('A').
        self::assertSame('A', Str::stripCSlashes('\\x41'));
    }

    public function testStripCSlashesNewline(): void
    {
        self::assertSame("line\n", Str::stripCSlashes('line\\n'));
    }

    public function testStripCSlashesRoundtripsAddCSlashes(): void
    {
        $original = 'Hello World';
        self::assertSame($original, Str::stripCSlashes(Str::addCSlashes($original, 'A..Z')));
    }

    public function testStripCSlashesEmpty(): void
    {
        self::assertSame('', Str::stripCSlashes(''));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function quoteMetaProvider(): iterable
    {
        yield 'arithmetic regex metas' => ['1+2.5*3', '1\\+2\\.5\\*3'];
        yield 'parentheses' => ['(a|b)', '\\(a|b\\)'];
        yield 'no metas' => ['hello', 'hello'];
        yield 'empty' => ['', ''];
    }

    #[DataProvider('quoteMetaProvider')]
    public function testQuoteMeta(string $input, string $expected): void
    {
        self::assertSame($expected, Str::quoteMeta($input));
    }
}
