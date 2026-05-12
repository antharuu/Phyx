<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use Phyx\Enums\Comparison;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class)]
final class HandleSearchTest extends TestCase
{
    public function testSearchesStrictlyAndLoosely(): void
    {
        $array = ['a' => '1', 'b' => 2, 'c' => null];

        self::assertFalse(Arr::contains($array, 1));
        self::assertTrue(Arr::contains($array, 1, Comparison::Loose));
        self::assertNull(Arr::indexOf($array, 1));
        self::assertSame('a', Arr::indexOf($array, 1, Comparison::Loose));
        self::assertSame('1', Arr::first($array));
        self::assertSame(2, Arr::first($array, static fn (mixed $value): bool => is_int($value)));
        self::assertSame('none', Arr::first([], null, 'none'));
        self::assertNull(Arr::last($array));
        self::assertSame('1', Arr::last($array, static fn (mixed $value): bool => is_string($value)));
        self::assertSame(['b' => 2], Arr::where($array, static fn (mixed $value): bool => is_int($value)));
        self::assertSame([2], Arr::where($array, static fn (mixed $value): bool => is_int($value), false));
        self::assertTrue(Arr::containsKey($array, 'c'));
    }
}
