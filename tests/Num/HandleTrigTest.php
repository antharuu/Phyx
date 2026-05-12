<?php

declare(strict_types=1);

namespace Phyx\Tests\Num;

use Phyx\Enums\AngleUnit;
use Phyx\Num;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Num::class)]
final class HandleTrigTest extends TestCase
{
    public function testTrigonometryAcceptsRadiansAndDegrees(): void
    {
        self::assertEqualsWithDelta(1.0, Num::sin(M_PI / 2), 0.0000000001);
        self::assertEqualsWithDelta(1.0, Num::sin(90, AngleUnit::Degrees), 0.0000000001);
        self::assertEqualsWithDelta(-1.0, Num::cos(180, AngleUnit::Degrees), 0.0000000001);
        self::assertEqualsWithDelta(1.0, Num::tan(45, AngleUnit::Degrees), 0.0000000001);
    }

    public function testInverseTrigonometryCanReturnRadiansOrDegrees(): void
    {
        self::assertEqualsWithDelta(M_PI / 2, Num::asin(1), 0.0000000001);
        self::assertEqualsWithDelta(90.0, Num::asin(1, AngleUnit::Degrees), 0.0000000001);
        self::assertEqualsWithDelta(180.0, Num::acos(-1, AngleUnit::Degrees), 0.0000000001);
        self::assertEqualsWithDelta(45.0, Num::atan(1, AngleUnit::Degrees), 0.0000000001);
    }

    public function testAngleConversions(): void
    {
        self::assertEqualsWithDelta(M_PI, Num::toRadians(180), 0.0000000001);
        self::assertEqualsWithDelta(180.0, Num::toDegrees(M_PI), 0.0000000001);
    }
}
