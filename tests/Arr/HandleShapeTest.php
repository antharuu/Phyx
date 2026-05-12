<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ValueError;

#[CoversClass(Arr::class)]
final class HandleShapeTest extends TestCase
{
    public function testFlattenCollapseAndDepth(): void
    {
        self::assertSame([1, 2, 3, 4], Arr::flatten([1, [2, [3]], 4]));
        self::assertSame([1, 2, [3], 4], Arr::flatten([1, [2, [3]], 4], 1));
        self::assertSame(['a' => [1]], Arr::flatten(['a' => [1]], 0));
        self::assertSame([1, 'name' => 'Ada', 2], Arr::collapse([[1], ['name' => 'Ada'], [2]]));
    }

    public function testChunkSliceTakeAndWrap(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];

        self::assertSame([['a' => 1, 'b' => 2], ['c' => 3]], Arr::chunk($array, 2, true));
        self::assertSame(['b' => 2, 'c' => 3], Arr::slice($array, 1));
        self::assertSame(['a' => 1, 'b' => 2], Arr::take($array, 2));
        self::assertSame(['b' => 2, 'c' => 3], Arr::take($array, -2));
        self::assertSame([], Arr::wrap(null));
        self::assertSame(['x'], Arr::wrap('x'));
        self::assertSame(['x' => 1], Arr::wrap(['x' => 1]));
    }

    public function testChunkRejectsInvalidSize(): void
    {
        $this->expectException(ValueError::class);

        Arr::chunk([1, 2], 0);
    }
}
