<?php

declare(strict_types=1);

namespace Phyx\Tests\Num;

use InvalidArgumentException;
use Phyx\Enums\Boundary;
use Phyx\Num;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Num::class)]
final class HandleRangeTest extends TestCase
{
    public function testClampRestrictsValueToRange(): void
    {
        self::assertSame(1, Num::clamp(-5, 1, 10));
        self::assertSame(10, Num::clamp(50, 1, 10));
        self::assertSame(5.5, Num::clamp(5.5, 1, 10));
    }

    public function testBetweenAndOutsideSupportInclusiveAndExclusiveBoundaries(): void
    {
        self::assertTrue(Num::between(1, 1, 3));
        self::assertTrue(Num::between(2, 1, 3, Boundary::Exclusive));
        self::assertFalse(Num::between(1, 1, 3, Boundary::Exclusive));
        self::assertFalse(Num::outside(3, 1, 3));
        self::assertTrue(Num::outside(3, 1, 3, Boundary::Exclusive));
    }

    public function testNormalizeMapsRangeToZeroOneScale(): void
    {
        self::assertSame(0.0, Num::normalize(10, 10, 20));
        self::assertSame(0.5, Num::normalize(15, 10, 20));
        self::assertSame(1.5, Num::normalize(25, 10, 20));
    }

    public function testInvalidRangesThrow(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Num::normalize(1, 1, 1);
    }
}
