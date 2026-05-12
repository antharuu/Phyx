<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use Phyx\Enums\Comparison;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class)]
final class HandleSetTest extends TestCase
{
    public function testUniqueAndDuplicatesSupportStrictAndLooseComparison(): void
    {
        self::assertSame(['a' => 1, 'b' => '1', 'd' => 2], Arr::unique(['a' => 1, 'b' => '1', 'c' => 1, 'd' => 2]));
        self::assertSame(['b' => '1', 'c' => 1], Arr::duplicates(['a' => 1, 'b' => '1', 'c' => 1], Comparison::Loose));
    }

    public function testDiffIntersectAndUnionPreservePredictableNativeSemantics(): void
    {
        self::assertSame(['a' => 1], Arr::diff(['a' => 1, 'b' => 2], [2]));
        self::assertSame(['b' => 2], Arr::intersect(['a' => 1, 'b' => 2], [2, 3]));
        self::assertSame(['a' => 1, 'b' => 2], Arr::union(['a' => 1], ['a' => 9, 'b' => 2]));
    }
}
