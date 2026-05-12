<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Enums\CaseSensitivity;
use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleReplaceTest extends TestCase
{
    // ─────────────────────────────────────────────────────────────────────
    // replace
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: string, 3: CaseSensitivity, 4: string}>
     */
    public static function replaceProvider(): iterable
    {
        yield 'sensitive single occurrence' => ['Hello World', 'World', 'Phyx', CaseSensitivity::Sensitive, 'Hello Phyx'];
        yield 'sensitive multiple occurrences' => ['ho ho ho', 'ho', 'ha', CaseSensitivity::Sensitive, 'ha ha ha'];
        yield 'sensitive no match' => ['Hello World', 'xyz', '!', CaseSensitivity::Sensitive, 'Hello World'];
        yield 'sensitive case respected' => ['Hello world', 'world', 'phyx', CaseSensitivity::Sensitive, 'Hello phyx'];
        yield 'insensitive single' => ['Hello World', 'hello', 'Hi', CaseSensitivity::Insensitive, 'Hi World'];
        yield 'insensitive multibyte' => ['École ÉCOLE école', 'école', '*', CaseSensitivity::Insensitive, '* * *'];
        yield 'empty needle is noop' => ['Hello', '', 'x', CaseSensitivity::Sensitive, 'Hello'];
        yield 'empty replacement removes' => ['ababab', 'b', '', CaseSensitivity::Sensitive, 'aaa'];
        yield 'replacement with regex metas safe' => ['Hello World', 'World', '$1.x', CaseSensitivity::Insensitive, 'Hello $1.x'];
    }

    #[DataProvider('replaceProvider')]
    public function testReplace(
        string $value,
        string $search,
        string $replacement,
        CaseSensitivity $case,
        string $expected,
    ): void {
        self::assertSame($expected, Str::replace($value, $search, $replacement, $case));
    }

    // ─────────────────────────────────────────────────────────────────────
    // replaceMany
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: array<string, string>, 2: CaseSensitivity, 3: string}>
     */
    public static function replaceManyProvider(): iterable
    {
        yield 'two substitutions sensitive' => [
            'Hello World',
            ['Hello' => 'Hi', 'World' => 'Phyx'],
            CaseSensitivity::Sensitive,
            'Hi Phyx',
        ];
        yield 'insensitive' => [
            'HELLO WORLD',
            ['hello' => 'Hi', 'world' => 'Phyx'],
            CaseSensitivity::Insensitive,
            'Hi Phyx',
        ];
        yield 'empty map is noop' => ['Hello', [], CaseSensitivity::Sensitive, 'Hello'];
        yield 'overlapping keys' => [
            'aaa',
            ['aa' => 'b'],
            CaseSensitivity::Sensitive,
            'ba',
        ];
        yield 'no match keeps input' => [
            'Hello',
            ['x' => 'y', 'z' => 'w'],
            CaseSensitivity::Sensitive,
            'Hello',
        ];
    }

    /**
     * @param array<string, string> $replacements
     */
    #[DataProvider('replaceManyProvider')]
    public function testReplaceMany(
        string $value,
        array $replacements,
        CaseSensitivity $case,
        string $expected,
    ): void {
        self::assertSame($expected, Str::replaceMany($value, $replacements, $case));
    }

    // ─────────────────────────────────────────────────────────────────────
    // replaceSlice
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: string, 2: int, 3: ?int, 4: string}>
     */
    public static function replaceSliceProvider(): iterable
    {
        yield 'positive start no length replaces to end' => ['Hello World', 'Phyx', 6, null, 'Hello Phyx'];
        yield 'positive start with length' => ['Hello World', '!', 5, 6, 'Hello!'];
        yield 'negative start' => ['Hello World', '*', -5, 5, 'Hello *'];
        yield 'negative length' => ['Hello World', '*', 0, -6, '* World'];
        yield 'zero length inserts' => ['Hello', '!', 5, 0, 'Hello!'];
        yield 'multibyte slice' => ['café', 'A', 2, 1, 'caAé'];
        yield 'start beyond length appends' => ['Hello', '!', 100, null, 'Hello!'];
        yield 'empty replacement removes' => ['Hello World', '', 5, 6, 'Hello'];
        yield 'empty value with replacement' => ['', 'abc', 0, null, 'abc'];
    }

    #[DataProvider('replaceSliceProvider')]
    public function testReplaceSlice(
        string $value,
        string $replacement,
        int $start,
        ?int $length,
        string $expected,
    ): void {
        self::assertSame($expected, Str::replaceSlice($value, $replacement, $start, $length));
    }

    // ─────────────────────────────────────────────────────────────────────
    // translate
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: array<string, string>, 2: string}>
     */
    public static function translateProvider(): iterable
    {
        yield 'two mappings' => [
            'hello world',
            ['hello' => 'bonjour', 'world' => 'monde'],
            'bonjour monde',
        ];
        yield 'no cascade between substitutions' => [
            'ab',
            ['a' => 'b', 'b' => 'a'],
            'ba',
        ];
        yield 'empty mapping is noop' => ['hello', [], 'hello'];
        yield 'single char' => ['hello', ['l' => 'L'], 'heLLo'];
        yield 'unmatched mapping' => ['hello', ['xyz' => '...'], 'hello'];
        yield 'multibyte key' => ['café', ['é' => 'e'], 'cafe'];
    }

    /**
     * @param array<string, string> $mapping
     */
    #[DataProvider('translateProvider')]
    public function testTranslate(string $value, array $mapping, string $expected): void
    {
        self::assertSame($expected, Str::translate($value, $mapping));
    }

    // ─────────────────────────────────────────────────────────────────────
    // rot13
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function rot13Provider(): iterable
    {
        yield 'classic ascii' => ['Hello', 'Uryyb'];
        yield 'inverse round trip' => ['Uryyb', 'Hello'];
        yield 'digits untouched' => ['abc123', 'nop123'];
        yield 'multibyte untouched' => ['café', 'pnsé'];
        yield 'empty' => ['', ''];
    }

    #[DataProvider('rot13Provider')]
    public function testRot13(string $input, string $expected): void
    {
        self::assertSame($expected, Str::rot13($input));
    }
}
