<?php

declare(strict_types=1);

namespace Phyx\Tests\Num;

use Phyx\Enums\NumberFormat;
use Phyx\Num;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Num::class)]
final class HandleFormatTest extends TestCase
{
    public function testFormatUsesNumberFormatSeparators(): void
    {
        self::assertSame('1,234.57', Num::format(1234.567, 2));
        self::assertSame('1 234,57', Num::format(1234.567, 2, ',', ' '));
        self::assertSame('1.2K', Num::format(1234, 1, '.', ',', NumberFormat::Compact));
        self::assertSame('1.0M', Num::format(1_000_000, 1, '.', ',', NumberFormat::Compact));
        self::assertSame('1.0B', Num::format(1_000_000_000, 1, '.', ',', NumberFormat::Compact));
        self::assertSame('999.0', Num::format(999, 1, '.', ',', NumberFormat::Compact));
    }

    public function testToStringHandlesIntegersFloatsAndSpecialFloats(): void
    {
        self::assertSame('12', Num::toString(12));
        self::assertSame('12.5', Num::toString(12.5));
        self::assertSame('INF', Num::toString(INF));
        self::assertSame('-INF', Num::toString(-INF));
        self::assertSame('NAN', Num::toString(NAN));
    }

    public function testOrdinalUsesEnglishFallback(): void
    {
        self::assertSame('1st', Num::ordinal(1));
        self::assertSame('2nd', Num::ordinal(2));
        self::assertSame('3rd', Num::ordinal(3));
        self::assertSame('4th', Num::ordinal(4));
        self::assertSame('11th', Num::ordinal(11));
        self::assertSame('23rd', Num::ordinal(23));
        self::assertSame('-1st', Num::ordinal(-1));
        self::assertSame('1er', Num::ordinal(1, 'fr_FR'));
    }
}
