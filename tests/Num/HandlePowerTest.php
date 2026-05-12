<?php

declare(strict_types=1);

namespace Phyx\Tests\Num;

use InvalidArgumentException;
use Phyx\Num;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Num::class)]
final class HandlePowerTest extends TestCase
{
    public function testPowerRootLogAndExp(): void
    {
        self::assertSame(8, Num::power(2, 3));
        self::assertSame(0.25, Num::power(2, -2));
        self::assertSame(3.0, Num::sqrt(9));
        self::assertEqualsWithDelta(3.0, Num::log(8, 2), 0.0000000001);
        self::assertEqualsWithDelta(M_E, Num::exp(1), 0.0000000001);
    }

    public function testInvalidLogValueThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Num::log(0, 10);
    }

    public function testInvalidLogBaseThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Num::log(10, 1);
    }
}
