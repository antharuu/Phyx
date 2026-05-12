<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Enums\CaseSensitivity;
use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleSearchTest extends TestCase
{
    // ─────────────────────────────────────────────────────────────────────
    // contains
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: CaseSensitivity, 3: bool}>
     */
    public static function containsProvider(): iterable
    {
        yield 'sensitive found' => ['Hello World', 'World', CaseSensitivity::Sensitive, true];
        yield 'sensitive not found wrong case' => ['Hello World', 'world', CaseSensitivity::Sensitive, false];
        yield 'sensitive not found absent' => ['Hello World', 'xyz', CaseSensitivity::Sensitive, false];
        yield 'insensitive found' => ['Hello World', 'world', CaseSensitivity::Insensitive, true];
        yield 'insensitive found uppercase needle' => ['Hello World', 'HELLO', CaseSensitivity::Insensitive, true];
        yield 'multibyte sensitive' => ['café', 'fé', CaseSensitivity::Sensitive, true];
        yield 'multibyte insensitive' => ['CAFÉ', 'café', CaseSensitivity::Insensitive, true];
        yield 'empty needle' => ['hello', '', CaseSensitivity::Sensitive, true];
        yield 'empty value with empty needle' => ['', '', CaseSensitivity::Sensitive, true];
        yield 'empty value with needle' => ['', 'a', CaseSensitivity::Sensitive, false];
    }

    #[DataProvider('containsProvider')]
    public function testContains(string $value, string $search, CaseSensitivity $case, bool $expected): void
    {
        self::assertSame($expected, Str::contains($value, $search, $case));
    }

    // ─────────────────────────────────────────────────────────────────────
    // startsWith
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: CaseSensitivity, 3: bool}>
     */
    public static function startsWithProvider(): iterable
    {
        yield 'sensitive match' => ['Hello World', 'Hello', CaseSensitivity::Sensitive, true];
        yield 'sensitive no match' => ['Hello World', 'hello', CaseSensitivity::Sensitive, false];
        yield 'insensitive match' => ['Hello World', 'hello', CaseSensitivity::Insensitive, true];
        yield 'insensitive multibyte' => ['École', 'école', CaseSensitivity::Insensitive, true];
        yield 'empty needle' => ['hello', '', CaseSensitivity::Sensitive, true];
        yield 'value shorter than needle' => ['hi', 'hello', CaseSensitivity::Sensitive, false];
    }

    #[DataProvider('startsWithProvider')]
    public function testStartsWith(string $value, string $search, CaseSensitivity $case, bool $expected): void
    {
        self::assertSame($expected, Str::startsWith($value, $search, $case));
    }

    // ─────────────────────────────────────────────────────────────────────
    // endsWith
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: CaseSensitivity, 3: bool}>
     */
    public static function endsWithProvider(): iterable
    {
        yield 'sensitive match' => ['Hello World', 'World', CaseSensitivity::Sensitive, true];
        yield 'sensitive no match' => ['Hello World', 'world', CaseSensitivity::Sensitive, false];
        yield 'insensitive match' => ['Hello World', 'WORLD', CaseSensitivity::Insensitive, true];
        yield 'insensitive multibyte' => ['école', 'ÉCOLE', CaseSensitivity::Insensitive, true];
        yield 'empty needle' => ['hello', '', CaseSensitivity::Sensitive, true];
        yield 'value shorter than needle' => ['hi', 'hello', CaseSensitivity::Sensitive, false];
    }

    #[DataProvider('endsWithProvider')]
    public function testEndsWith(string $value, string $search, CaseSensitivity $case, bool $expected): void
    {
        self::assertSame($expected, Str::endsWith($value, $search, $case));
    }

    // ─────────────────────────────────────────────────────────────────────
    // indexOf
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: CaseSensitivity, 3: ?int}>
     */
    public static function indexOfProvider(): iterable
    {
        yield 'sensitive found' => ['Hello World', 'World', CaseSensitivity::Sensitive, 6];
        yield 'sensitive not found' => ['Hello World', 'world', CaseSensitivity::Sensitive, null];
        yield 'insensitive found' => ['Hello World', 'world', CaseSensitivity::Insensitive, 6];
        yield 'multibyte found' => ['café', 'fé', CaseSensitivity::Sensitive, 2];
        yield 'multibyte position counts characters' => ['écafé', 'fé', CaseSensitivity::Sensitive, 3];
        yield 'empty needle returns null' => ['hello', '', CaseSensitivity::Sensitive, null];
        yield 'needle absent' => ['hello', 'xyz', CaseSensitivity::Sensitive, null];
        yield 'needle at start' => ['hello', 'he', CaseSensitivity::Sensitive, 0];
    }

    #[DataProvider('indexOfProvider')]
    public function testIndexOf(string $value, string $search, CaseSensitivity $case, ?int $expected): void
    {
        self::assertSame($expected, Str::indexOf($value, $search, $case));
    }

    // ─────────────────────────────────────────────────────────────────────
    // lastIndexOf
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: CaseSensitivity, 3: ?int}>
     */
    public static function lastIndexOfProvider(): iterable
    {
        yield 'sensitive found' => ['World Hello World', 'World', CaseSensitivity::Sensitive, 12];
        yield 'sensitive not found' => ['Hello World', 'xyz', CaseSensitivity::Sensitive, null];
        yield 'insensitive found' => ['World Hello world', 'WORLD', CaseSensitivity::Insensitive, 12];
        yield 'multibyte found' => ['café café', 'fé', CaseSensitivity::Sensitive, 7];
        yield 'empty needle returns null' => ['hello', '', CaseSensitivity::Sensitive, null];
    }

    #[DataProvider('lastIndexOfProvider')]
    public function testLastIndexOf(string $value, string $search, CaseSensitivity $case, ?int $expected): void
    {
        self::assertSame($expected, Str::lastIndexOf($value, $search, $case));
    }

    // ─────────────────────────────────────────────────────────────────────
    // after / afterLast
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: CaseSensitivity, 3: ?string}>
     */
    public static function afterProvider(): iterable
    {
        yield 'found' => ['https://phyx.dev/docs', '://', CaseSensitivity::Sensitive, 'phyx.dev/docs'];
        yield 'first occurrence only' => ['a/b/c/d', '/', CaseSensitivity::Sensitive, 'b/c/d'];
        yield 'not found' => ['hello', 'xyz', CaseSensitivity::Sensitive, null];
        yield 'needle at end yields empty' => ['hello@', '@', CaseSensitivity::Sensitive, ''];
        yield 'insensitive' => ['Hello World', 'hello ', CaseSensitivity::Insensitive, 'World'];
        yield 'multibyte' => ['café-éclair', '-', CaseSensitivity::Sensitive, 'éclair'];
    }

    #[DataProvider('afterProvider')]
    public function testAfter(string $value, string $search, CaseSensitivity $case, ?string $expected): void
    {
        self::assertSame($expected, Str::after($value, $search, $case));
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2: CaseSensitivity, 3: ?string}>
     */
    public static function afterLastProvider(): iterable
    {
        yield 'found multiple occurrences' => ['a/b/c/d', '/', CaseSensitivity::Sensitive, 'd'];
        yield 'single occurrence' => ['hello@phyx.dev', '@', CaseSensitivity::Sensitive, 'phyx.dev'];
        yield 'not found' => ['hello', 'xyz', CaseSensitivity::Sensitive, null];
        yield 'needle at end yields empty' => ['hello/', '/', CaseSensitivity::Sensitive, ''];
        yield 'insensitive' => ['World Hello world', 'WORLD', CaseSensitivity::Insensitive, ''];
    }

    #[DataProvider('afterLastProvider')]
    public function testAfterLast(string $value, string $search, CaseSensitivity $case, ?string $expected): void
    {
        self::assertSame($expected, Str::afterLast($value, $search, $case));
    }

    // ─────────────────────────────────────────────────────────────────────
    // before / beforeLast
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: CaseSensitivity, 3: ?string}>
     */
    public static function beforeProvider(): iterable
    {
        yield 'found' => ['https://phyx.dev', '://', CaseSensitivity::Sensitive, 'https'];
        yield 'first occurrence only' => ['a/b/c/d', '/', CaseSensitivity::Sensitive, 'a'];
        yield 'not found' => ['hello', 'xyz', CaseSensitivity::Sensitive, null];
        yield 'needle at start yields empty' => ['@hello', '@', CaseSensitivity::Sensitive, ''];
        yield 'insensitive' => ['World hello', 'HELLO', CaseSensitivity::Insensitive, 'World '];
    }

    #[DataProvider('beforeProvider')]
    public function testBefore(string $value, string $search, CaseSensitivity $case, ?string $expected): void
    {
        self::assertSame($expected, Str::before($value, $search, $case));
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2: CaseSensitivity, 3: ?string}>
     */
    public static function beforeLastProvider(): iterable
    {
        yield 'found multiple occurrences' => ['a/b/c/d', '/', CaseSensitivity::Sensitive, 'a/b/c'];
        yield 'single occurrence' => ['hello@phyx.dev', '@', CaseSensitivity::Sensitive, 'hello'];
        yield 'not found' => ['hello', 'xyz', CaseSensitivity::Sensitive, null];
        yield 'needle at start yields empty' => ['/hello', '/', CaseSensitivity::Sensitive, ''];
        yield 'insensitive' => ['World Hello WORLD', 'world', CaseSensitivity::Insensitive, 'World Hello '];
    }

    #[DataProvider('beforeLastProvider')]
    public function testBeforeLast(string $value, string $search, CaseSensitivity $case, ?string $expected): void
    {
        self::assertSame($expected, Str::beforeLast($value, $search, $case));
    }

    // ─────────────────────────────────────────────────────────────────────
    // firstOf
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: CaseSensitivity, 3: ?string}>
     */
    public static function firstOfProvider(): iterable
    {
        yield 'found includes needle' => ['hello@phyx.dev', '@', CaseSensitivity::Sensitive, '@phyx.dev'];
        yield 'not found' => ['hello', 'xyz', CaseSensitivity::Sensitive, null];
        yield 'empty needle returns null' => ['hello', '', CaseSensitivity::Sensitive, null];
        yield 'insensitive' => ['Hello World', 'WORLD', CaseSensitivity::Insensitive, 'World'];
        yield 'multibyte' => ['café-éclair', 'éclair', CaseSensitivity::Sensitive, 'éclair'];
    }

    #[DataProvider('firstOfProvider')]
    public function testFirstOf(string $value, string $search, CaseSensitivity $case, ?string $expected): void
    {
        self::assertSame($expected, Str::firstOf($value, $search, $case));
    }

    // ─────────────────────────────────────────────────────────────────────
    // wordCount
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function wordCountProvider(): iterable
    {
        yield 'multi words' => ['Hello brave new world', 4];
        yield 'single word' => ['hello', 1];
        yield 'empty string' => ['', 0];
        yield 'whitespace only' => ['   ', 0];
        yield 'punctuation between words' => ['hello, world!', 2];
        yield 'contraction counted as one' => ["don't be afraid", 3];
        yield 'multibyte words' => ['école polytechnique', 2];
        yield 'digits are not letters' => ['abc 123 def', 2];
    }

    #[DataProvider('wordCountProvider')]
    public function testWordCount(string $input, int $expected): void
    {
        self::assertSame($expected, Str::wordCount($input));
    }

    // ─────────────────────────────────────────────────────────────────────
    // occurrences
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{string, string, int}>
     */
    public static function occurrencesProvider(): iterable
    {
        yield 'basic count' => ['banana', 'a', 3];
        yield 'no occurrences' => ['hello', 'z', 0];
        yield 'empty needle returns zero' => ['hello', '', 0];
        yield 'non-overlapping' => ['aaaa', 'aa', 2];
        yield 'multibyte needle' => ['café café café', 'é', 3];
        yield 'whole string needle' => ['hello', 'hello', 1];
    }

    #[DataProvider('occurrencesProvider')]
    public function testOccurrences(string $value, string $search, int $expected): void
    {
        self::assertSame($expected, Str::occurrences($value, $search));
    }

    // ─────────────────────────────────────────────────────────────────────
    // span / cspan
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{string, string, int}>
     */
    public static function spanProvider(): iterable
    {
        yield 'all match' => ['aaaa', 'a', 4];
        yield 'partial match' => ['aaabbb', 'a', 3];
        yield 'multi-char set' => ['123abc', '0123456789', 3];
        yield 'no match at start' => ['xyz', 'abc', 0];
        yield 'empty value' => ['', 'abc', 0];
        yield 'empty chars yields zero' => ['abc', '', 0];
        yield 'multibyte set' => ['ééé!', 'é', 3];
    }

    #[DataProvider('spanProvider')]
    public function testSpan(string $value, string $chars, int $expected): void
    {
        self::assertSame($expected, Str::span($value, $chars));
    }

    /**
     * @return iterable<string, array{string, string, int}>
     */
    public static function cspanProvider(): iterable
    {
        yield 'all forbidden absent' => ['abc', '0123456789', 3];
        yield 'partial' => ['abc123', '0123456789', 3];
        yield 'first char forbidden' => ['1abc', '0123456789', 0];
        yield 'empty value' => ['', 'abc', 0];
        yield 'empty chars yields length' => ['abc', '', 3];
        yield 'multibyte' => ['hello!é', 'é', 6];
    }

    #[DataProvider('cspanProvider')]
    public function testCspan(string $value, string $chars, int $expected): void
    {
        self::assertSame($expected, Str::cspan($value, $chars));
    }
}
