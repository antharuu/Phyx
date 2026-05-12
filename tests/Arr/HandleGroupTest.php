<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class)]
final class HandleGroupTest extends TestCase
{
    public function testPluckCanUseValueAndKeyColumns(): void
    {
        $rows = [
            ['id' => 'a', 'name' => 'Ada'],
            ['id' => 'g', 'name' => 'Grace'],
            ['id' => 'x'],
            'ignored',
        ];

        self::assertSame(['Ada', 'Grace'], Arr::pluck($rows, 'name'));
        self::assertSame(['a' => 'Ada', 'g' => 'Grace'], Arr::pluck($rows, 'name', 'id'));
    }

    public function testGroupKeyAndPartitionUseValueThenKey(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];

        self::assertSame([
            'odd' => [1, 3],
            'small' => [1, 3],
            'even' => [2],
        ], Arr::groupBy($array, static fn (int $value): array|string => $value % 2 === 0 ? 'even' : ['odd', 'small']));

        self::assertSame(['item-a' => 1, 'item-b' => 2, 'item-c' => 3], Arr::keyBy($array, static fn (int $_value, string $key): string => 'item-' . $key));
        self::assertSame([['b' => 2], ['a' => 1, 'c' => 3]], Arr::partition($array, static fn (int $value): bool => $value % 2 === 0));
    }
}
