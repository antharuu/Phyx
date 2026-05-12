<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleFormatTest extends TestCase
{
    // ─────────────────────────────────────────────────────────────────────
    // format
    // ─────────────────────────────────────────────────────────────────────

    public function testFormatString(): void
    {
        self::assertSame(
            'Hello world, you are 42',
            Str::format('Hello %s, you are %d', 'world', 42),
        );
    }

    public function testFormatFloat(): void
    {
        self::assertSame('03.14', Str::format('%05.2f', 3.14));
    }

    public function testFormatNoArgs(): void
    {
        self::assertSame('literal', Str::format('literal'));
    }

    public function testFormatMultipleTypes(): void
    {
        self::assertSame(
            'name=phyx age=2 score=99.5',
            Str::format('name=%s age=%d score=%.1f', 'phyx', 2, 99.5),
        );
    }

    public function testFormatPercentLiteral(): void
    {
        self::assertSame('100% done', Str::format('%d%% done', 100));
    }

    // ─────────────────────────────────────────────────────────────────────
    // formatArgs
    // ─────────────────────────────────────────────────────────────────────

    public function testFormatArgs(): void
    {
        self::assertSame(
            'Hello world, you are 42',
            Str::formatArgs('Hello %s, you are %d', ['world', 42]),
        );
    }

    public function testFormatArgsEmpty(): void
    {
        self::assertSame('literal', Str::formatArgs('literal', []));
    }

    public function testFormatArgsMultipleTypes(): void
    {
        self::assertSame(
            'a=1 b=2.5 c=hi',
            Str::formatArgs('a=%d b=%.1f c=%s', [1, 2.5, 'hi']),
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // formatNumber
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: float, 1: int, 2: string, 3: string, 4: string}>
     */
    public static function formatNumberProvider(): iterable
    {
        yield 'integer rounded' => [1234567.891, 0, '.', ',', '1,234,568'];
        yield 'two decimals' => [1234567.891, 2, '.', ',', '1,234,567.89'];
        yield 'french style' => [1234567.891, 2, ',', ' ', '1 234 567,89'];
        yield 'negative number' => [-1234.5, 2, '.', ',', '-1,234.50'];
        yield 'zero' => [0.0, 2, '.', ',', '0.00'];
        yield 'small value' => [0.123, 3, '.', ',', '0.123'];
        yield 'integer no decimals' => [1000.0, 0, '.', ',', '1,000'];
        yield 'negative decimals rounds to power of ten' => [1234.5, -3, '.', ',', '1,000'];
    }

    #[DataProvider('formatNumberProvider')]
    public function testFormatNumber(
        float $number,
        int $decimals,
        string $decimalSep,
        string $thousandsSep,
        string $expected,
    ): void {
        self::assertSame($expected, Str::formatNumber($number, $decimals, $decimalSep, $thousandsSep));
    }

    public function testFormatNumberDefaults(): void
    {
        self::assertSame('1,234,568', Str::formatNumber(1234567.891));
    }
}
