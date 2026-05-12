<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class)]
final class HandleInspectTest extends TestCase
{
    public function testInspectsShapeAndKeys(): void
    {
        self::assertTrue(Arr::isList([1, 2]));
        self::assertFalse(Arr::isList([1 => 'a']));
        self::assertFalse(Arr::isAssoc([]));
        self::assertTrue(Arr::isAssoc(['a' => 1]));
        self::assertTrue(Arr::isEmpty([]));
        self::assertFalse(Arr::isEmpty([null]));
        self::assertTrue(Arr::isNotEmpty([null]));
        self::assertSame(2, Arr::count(['a' => 1, 'b' => 2]));
        self::assertSame(['a', 'b'], Arr::keys(['a' => 1, 'b' => 2]));
        self::assertSame([1, 2], Arr::values(['a' => 1, 'b' => 2]));
        self::assertSame('a', Arr::firstKey(['a' => 1, 'b' => 2]));
        self::assertSame('b', Arr::lastKey(['a' => 1, 'b' => 2]));
        self::assertNull(Arr::firstKey([]));
        self::assertNull(Arr::lastKey([]));
    }
}
