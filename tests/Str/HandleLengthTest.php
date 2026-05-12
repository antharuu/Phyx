<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleLengthTest extends TestCase
{
    /**
     * @return iterable<string, array{string, int}>
     */
    public static function lengthProvider(): iterable
    {
        yield 'ascii' => ['hello', 5];
        yield 'empty' => ['', 0];
        yield 'single char' => ['a', 1];
        yield 'multibyte accented' => ['café', 4];
        yield 'multibyte full' => ['élève', 5];
        yield 'emoji' => ['👋hi', 3];
        yield 'whitespace counted' => [' a b ', 5];
    }

    #[DataProvider('lengthProvider')]
    public function testLength(string $input, int $expected): void
    {
        self::assertSame($expected, Str::length($input));
    }

    /**
     * @return iterable<string, array{0: string, 1: array<string, int>}>
     */
    public static function charStatsProvider(): iterable
    {
        yield 'empty' => ['', []];
        yield 'single char' => ['a', ['a' => 1]];
        yield 'all distinct' => ['abc', ['a' => 1, 'b' => 1, 'c' => 1]];
        yield 'duplicates' => ['aabc', ['a' => 2, 'b' => 1, 'c' => 1]];
        yield 'multibyte' => ['élève', ['é' => 1, 'l' => 1, 'è' => 1, 'v' => 1, 'e' => 1]];
        yield 'with whitespace' => ['a a', ['a' => 2, ' ' => 1]];
    }

    /**
     * @param array<string, int> $expected
     */
    #[DataProvider('charStatsProvider')]
    public function testCharStats(string $input, array $expected): void
    {
        self::assertSame($expected, Str::charStats($input));
    }
}
