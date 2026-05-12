<?php

declare(strict_types=1);

namespace Phyx\Tests\Num;

use Phyx\Enums\Rounding;
use Phyx\Num;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Num::class)]
final class HandleRoundTest extends TestCase
{
    public function testRoundSupportsEveryRoundingMode(): void
    {
        self::assertSame(3.0, Num::round(2.5, 0, Rounding::HalfUp));
        self::assertSame(2.0, Num::round(2.5, 0, Rounding::HalfDown));
        self::assertSame(2.0, Num::round(2.5, 0, Rounding::HalfEven));
        self::assertSame(3.0, Num::round(2.5, 0, Rounding::HalfOdd));
        self::assertSame(2.0, Num::round(2.9, 0, Rounding::TowardZero));
        self::assertSame(-2.0, Num::round(-2.9, 0, Rounding::TowardZero));
        self::assertSame(3.0, Num::round(2.1, 0, Rounding::AwayFromZero));
        self::assertSame(-3.0, Num::round(-2.1, 0, Rounding::AwayFromZero));
        self::assertSame(1.24, Num::round(1.235, 2, Rounding::HalfUp));
    }

    public function testCeilFloorAndTruncate(): void
    {
        self::assertSame(3.0, Num::ceil(2.1));
        self::assertSame(-2.0, Num::ceil(-2.9));
        self::assertSame(2.0, Num::floor(2.9));
        self::assertSame(-3.0, Num::floor(-2.1));
        self::assertSame(2, Num::truncate(2.9));
        self::assertSame(-2, Num::truncate(-2.9));
    }
}
