<?php

declare(strict_types=1);

namespace Phyx\Tests\Num;

use Phyx\Num;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Num::class)]
final class HandleCheckTest extends TestCase
{
    public function testIntegerParity(): void
    {
        self::assertTrue(Num::isEven(0));
        self::assertTrue(Num::isEven(-4));
        self::assertFalse(Num::isEven(5));
        self::assertTrue(Num::isOdd(-3));
        self::assertFalse(Num::isOdd(8));
    }

    public function testSignAndZeroPredicates(): void
    {
        self::assertTrue(Num::isPositive(0.1));
        self::assertFalse(Num::isPositive(0));
        self::assertTrue(Num::isNegative(-0.1));
        self::assertFalse(Num::isNegative(0));
        self::assertTrue(Num::isZero(0.0));
        self::assertFalse(Num::isZero(1));
    }

    public function testFloatStatePredicates(): void
    {
        self::assertTrue(Num::isFinite(1.5));
        self::assertFalse(Num::isFinite(INF));
        self::assertTrue(Num::isInfinite(-INF));
        self::assertFalse(Num::isInfinite(1.0));
        self::assertTrue(Num::isNan(NAN));
        self::assertFalse(Num::isNan(1.0));
    }
}
