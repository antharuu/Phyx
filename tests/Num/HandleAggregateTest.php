<?php

declare(strict_types=1);

namespace Phyx\Tests\Num;

use Phyx\Num;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Num::class)]
final class HandleAggregateTest extends TestCase
{
    public function testEmptyAggregatesReturnPredictableValues(): void
    {
        self::assertNull(Num::min([]));
        self::assertNull(Num::max([]));
        self::assertSame(0, Num::sum([]));
        self::assertNull(Num::average([]));
        self::assertNull(Num::median([]));
    }

    public function testAggregatesNumbers(): void
    {
        $numbers = [5, -2, 10.5, 4];

        self::assertSame(-2, Num::min($numbers));
        self::assertSame(10.5, Num::max($numbers));
        self::assertSame(17.5, Num::sum($numbers));
        self::assertSame(4.375, Num::average($numbers));
        self::assertSame(4.5, Num::median([5, 1, 4, 10]));
        self::assertSame(4.0, Num::median([5, 1, 4]));
    }
}
