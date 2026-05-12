<?php

declare(strict_types=1);

namespace Phyx\Tests\Num;

use DivisionByZeroError;
use InvalidArgumentException;
use Phyx\Num;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Num::class)]
final class HandleConvertTest extends TestCase
{
    public function testBaseConversionsCoverRequiredBases(): void
    {
        self::assertSame('1010', Num::toBase(10, 2));
        self::assertSame('12', Num::toBase(10, 8));
        self::assertSame('10', Num::toBase(10, 10));
        self::assertSame('ff', Num::toBase(255, 16));
        self::assertSame('z', Num::toBase(35, 36));
        self::assertSame(10, Num::fromBase('1010', 2));
        self::assertSame(10, Num::fromBase('12', 8));
        self::assertSame(10, Num::fromBase('10', 10));
        self::assertSame(255, Num::fromBase('ff', 16));
        self::assertSame(35, Num::fromBase('z', 36));
        self::assertSame(1.0E+36, Num::fromBase('1000000000000000000000000000000000000', 10));
        self::assertSame('-ff', Num::convertBase('-255', 10, 16));
    }

    public function testInvalidBaseAndDigitsThrow(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Num::convertBase('10', 1, 10);
    }

    public function testInvalidDigitsThrow(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Num::fromBase('2', 2);
    }

    public function testEmptyBaseValueThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Num::fromBase('', 10);
    }

    public function testRatioAndPercentage(): void
    {
        self::assertSame(0.25, Num::ratio(5, 20));
        self::assertSame(25.0, Num::percentage(5, 20));
    }

    public function testRatioWithZeroTotalThrows(): void
    {
        $this->expectException(DivisionByZeroError::class);
        Num::ratio(1, 0);
    }
}
