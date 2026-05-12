<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Enums\CaseSensitivity;
use Phyx\Enums\Ordering;
use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleCompareTest extends TestCase
{
    // ─────────────────────────────────────────────────────────────────────
    // compare — binary
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: CaseSensitivity, 3: Ordering, 4: int}>
     */
    public static function compareProvider(): iterable
    {
        // Binary sensitive
        yield 'binary sensitive equal' => ['apple', 'apple', CaseSensitivity::Sensitive, Ordering::Binary, 0];
        yield 'binary sensitive less' => ['apple', 'banana', CaseSensitivity::Sensitive, Ordering::Binary, -1];
        yield 'binary sensitive greater' => ['banana', 'apple', CaseSensitivity::Sensitive, Ordering::Binary, 1];
        yield 'binary sensitive case matters' => ['Apple', 'apple', CaseSensitivity::Sensitive, Ordering::Binary, -1];

        // Binary insensitive
        yield 'binary insensitive equal' => ['Apple', 'apple', CaseSensitivity::Insensitive, Ordering::Binary, 0];
        yield 'binary insensitive less' => ['apple', 'BANANA', CaseSensitivity::Insensitive, Ordering::Binary, -1];

        // Natural sensitive
        yield 'natural sensitive item2 vs item10' => ['item2', 'item10', CaseSensitivity::Sensitive, Ordering::Natural, -1];
        yield 'natural sensitive equal' => ['file5', 'file5', CaseSensitivity::Sensitive, Ordering::Natural, 0];

        // Natural insensitive
        yield 'natural insensitive' => ['File2', 'file10', CaseSensitivity::Insensitive, Ordering::Natural, -1];

        // Locale (binary expected since LC_COLLATE = C)
        yield 'locale sensitive equal' => ['apple', 'apple', CaseSensitivity::Sensitive, Ordering::Locale, 0];

        // Multibyte sanity
        yield 'multibyte equal' => ['café', 'café', CaseSensitivity::Sensitive, Ordering::Binary, 0];
        yield 'multibyte insensitive equal' => ['CAFÉ', 'café', CaseSensitivity::Insensitive, Ordering::Binary, 0];
    }

    #[DataProvider('compareProvider')]
    public function testCompare(
        string $a,
        string $b,
        CaseSensitivity $case,
        Ordering $ordering,
        int $expected,
    ): void {
        self::assertSame($expected, Str::compare($a, $b, $case, $ordering));
    }

    public function testCompareDefaultsAreSensitiveAndBinary(): void
    {
        self::assertSame(-1, Str::compare('Apple', 'apple'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // comparePrefix
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: int, 3: CaseSensitivity, 4: int}>
     */
    public static function comparePrefixProvider(): iterable
    {
        yield 'equal prefixes' => ['abcdef', 'abcxyz', 3, CaseSensitivity::Sensitive, 0];
        yield 'different prefixes' => ['abcdef', 'abdxyz', 3, CaseSensitivity::Sensitive, -1];
        yield 'insensitive equal prefix' => ['Abc', 'abc', 3, CaseSensitivity::Insensitive, 0];
        yield 'zero length is equal' => ['abc', 'xyz', 0, CaseSensitivity::Sensitive, 0];
        yield 'negative length is equal' => ['abc', 'xyz', -5, CaseSensitivity::Sensitive, 0];
        yield 'length exceeds string' => ['ab', 'ab', 100, CaseSensitivity::Sensitive, 0];
        yield 'multibyte prefix' => ['café', 'capo', 2, CaseSensitivity::Sensitive, 0];
    }

    #[DataProvider('comparePrefixProvider')]
    public function testComparePrefix(
        string $a,
        string $b,
        int $length,
        CaseSensitivity $case,
        int $expected,
    ): void {
        self::assertSame($expected, Str::comparePrefix($a, $b, $length, $case));
    }

    // ─────────────────────────────────────────────────────────────────────
    // similarity
    // ─────────────────────────────────────────────────────────────────────

    public function testSimilarityIdenticalReturns100(): void
    {
        $result = Str::similarity('hello', 'hello');

        self::assertSame(5, $result['matches']);
        self::assertSame(100.0, $result['percent']);
    }

    public function testSimilarityCompletelyDifferent(): void
    {
        $result = Str::similarity('abc', 'xyz');

        self::assertSame(0, $result['matches']);
        self::assertSame(0.0, $result['percent']);
    }

    public function testSimilarityPartialMatch(): void
    {
        $result = Str::similarity('Hello World', 'Hello Phyx');

        self::assertGreaterThan(0, $result['matches']);
        self::assertGreaterThan(0.0, $result['percent']);
        self::assertLessThan(100.0, $result['percent']);
    }

    public function testSimilarityEmpty(): void
    {
        $result = Str::similarity('', '');

        self::assertSame(0, $result['matches']);
        self::assertSame(0.0, $result['percent']);
    }

    // ─────────────────────────────────────────────────────────────────────
    // distance
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{string, string, int}>
     */
    public static function distanceProvider(): iterable
    {
        yield 'classic kitten to sitting' => ['kitten', 'sitting', 3];
        yield 'identical' => ['hello', 'hello', 0];
        yield 'one substitution' => ['abc', 'abd', 1];
        yield 'one insertion' => ['abc', 'abcd', 1];
        yield 'one deletion' => ['abcd', 'abc', 1];
        yield 'completely different' => ['abc', 'xyz', 3];
        yield 'empty to non-empty' => ['', 'abc', 3];
        yield 'both empty' => ['', '', 0];
    }

    #[DataProvider('distanceProvider')]
    public function testDistance(string $a, string $b, int $expected): void
    {
        self::assertSame($expected, Str::distance($a, $b));
    }

    public function testDistanceLargeInputs(): void
    {
        // Modern PHP builds (8.3 backport + 8.4+) compute Levenshtein on
        // inputs longer than the historical 255-byte cap. Phyx simply
        // forwards the native return; on legacy builds it can be -1.
        $tooLong = str_repeat('a', 256);
        // 5 substitutions on the first 5 chars + 251 deletions = 256 edits.
        self::assertSame(256, Str::distance($tooLong, 'hello'));
    }
}
