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

    public function testFindHelpersUseValueThenKeyAndShortCircuit(): void
    {
        $visited = [];
        $array = ['a' => 1, 'b' => 2, 'c' => 3];

        $found = Arr::find($array, static function (int $value, string $key) use (&$visited): bool {
            $visited[] = $key;
            return $value > 1;
        }, 'missing');

        self::assertSame(2, $found);
        self::assertSame(['a', 'b'], $visited);
        self::assertSame('missing', Arr::find($array, static fn (int $value): bool => $value > 9, 'missing'));
        self::assertSame('b', Arr::findKey($array, static fn (int $value): bool => $value === 2));
        self::assertNull(Arr::findKey($array, static fn (int $value): bool => $value > 9));
    }

    public function testAnyAndAllUsePredicateSemantics(): void
    {
        $array = ['a' => 2, 'b' => 4, 'c' => 6];

        self::assertTrue(Arr::any($array, static fn (int $value, string $key): bool => $key === 'b' && $value === 4));
        self::assertFalse(Arr::any($array, static fn (int $value): bool => $value > 9));
        self::assertTrue(Arr::all($array, static fn (int $value): bool => $value % 2 === 0));
        self::assertFalse(Arr::all($array, static fn (int $value): bool => $value < 5));
        self::assertFalse(Arr::any([], static fn (mixed $_value): bool => true));
        self::assertTrue(Arr::all([], static fn (mixed $_value): bool => false));
    }
}
