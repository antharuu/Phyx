<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Enums\CaseSensitivity;
use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleSliceTest extends TestCase
{
    /**
     * @return iterable<string, array{0: string, 1: int, 2: ?int, 3: string}>
     */
    public static function sliceProvider(): iterable
    {
        // Basic positive offset
        yield 'positive start no length' => ['Hello World', 6, null, 'World'];
        yield 'positive start with length' => ['Hello World', 0, 5, 'Hello'];

        // Negative start
        yield 'negative start no length' => ['Hello World', -5, null, 'World'];
        yield 'negative start with length' => ['Hello World', -5, 3, 'Wor'];

        // Negative length
        yield 'negative length excludes from end' => ['Hello World', 0, -6, 'Hello'];

        // Multibyte
        yield 'multibyte slice' => ['café', 2, null, 'fé'];
        yield 'multibyte single char' => ['café', 0, 1, 'c'];
        yield 'multibyte from end' => ['café', -1, null, 'é'];

        // Boundary
        yield 'start at zero' => ['hello', 0, null, 'hello'];
        yield 'start beyond length returns empty' => ['hello', 10, null, ''];
        yield 'length zero returns empty' => ['hello', 0, 0, ''];

        // Edge cases
        yield 'empty string' => ['', 0, null, ''];
        yield 'empty string with positive start' => ['', 5, 3, ''];
    }

    #[DataProvider('sliceProvider')]
    public function testSlice(string $input, int $start, ?int $length, string $expected): void
    {
        self::assertSame($expected, Str::slice($input, $start, $length));
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2: int, 3: ?int, 4: CaseSensitivity, 5: int}>
     */
    public static function sliceCompareProvider(): iterable
    {
        // Equal
        yield 'equal slice end of string' => ['Hello World', 'World', 6, null, CaseSensitivity::Sensitive, 0];
        yield 'equal slice with explicit length' => ['Hello World', 'World', 6, 5, CaseSensitivity::Sensitive, 0];
        yield 'equal slice case insensitive' => ['Hello World', 'world', 6, null, CaseSensitivity::Insensitive, 0];

        // Less than
        yield 'slice sorts before' => ['Hello World', 'world', 6, null, CaseSensitivity::Sensitive, -1];
        yield 'slice sorts before (length)' => ['abcDEF', 'abdxyz', 0, 3, CaseSensitivity::Sensitive, -1];

        // Greater than
        yield 'slice sorts after' => ['Hello world', 'WORLD', 6, null, CaseSensitivity::Sensitive, 1];

        // With explicit length truncation of $other
        yield 'length truncates other' => ['abcDEF', 'abcxyz', 0, 3, CaseSensitivity::Sensitive, 0];

        // Empty cases
        yield 'empty value yields empty slice equal to empty other' => ['', '', 0, null, CaseSensitivity::Sensitive, 0];

        // Negative start
        yield 'negative start equal' => ['Hello World', 'World', -5, null, CaseSensitivity::Sensitive, 0];
    }

    #[DataProvider('sliceCompareProvider')]
    public function testSliceCompare(
        string $value,
        string $other,
        int $start,
        ?int $length,
        CaseSensitivity $case,
        int $expected,
    ): void {
        self::assertSame($expected, Str::sliceCompare($value, $other, $start, $length, $case));
    }
}
