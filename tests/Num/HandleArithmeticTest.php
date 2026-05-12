<?php

declare(strict_types=1);

namespace Phyx\Tests\Num;

use DivisionByZeroError;
use Phyx\Num;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Num::class)]
final class HandleArithmeticTest extends TestCase
{
    public function testArithmeticHelpers(): void
    {
        self::assertSame(5, Num::abs(-5));
        self::assertSame(2.5, Num::abs(-2.5));
        self::assertSame(1.5, Num::mod(5.5, 2));
        self::assertSame(3, Num::divideInt(10, 3));
        self::assertSame(-1, Num::sign(-0.1));
        self::assertSame(0, Num::sign(0));
        self::assertSame(1, Num::sign(42));
        self::assertSame(25.0, Num::percentageOf(5, 20));
    }

    public function testModuloByZeroThrows(): void
    {
        $this->expectException(DivisionByZeroError::class);
        Num::mod(1, 0);
    }

    public function testIntegerDivisionByZeroThrows(): void
    {
        $this->expectException(DivisionByZeroError::class);
        Num::divideInt(1, 0);
    }

    public function testPercentageOfZeroThrows(): void
    {
        $this->expectException(DivisionByZeroError::class);
        Num::percentageOf(1, 0);
    }
}
