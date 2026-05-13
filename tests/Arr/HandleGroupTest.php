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

    public function testPluckSupportsNestedArrayAndObjectPaths(): void
    {
        $object = (object) [
            'id' => 'object-user',
            'profile' => (object) ['email' => 'object@example.test'],
        ];
        $rows = [
            ['id' => 'array-user', 'profile' => ['email' => 'array@example.test']],
            $object,
            ['id' => 'missing-email', 'profile' => []],
            ['profile' => ['email' => 'missing-key@example.test']],
        ];

        self::assertSame(['array@example.test', 'object@example.test', 'missing-key@example.test'], Arr::pluck($rows, 'profile.email'));
        self::assertSame([
            'array-user' => 'array@example.test',
            'object-user' => 'object@example.test',
        ], Arr::pluck($rows, 'profile.email', 'id'));
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

    public function testGroupByAndKeyBySupportSelectors(): void
    {
        $items = [
            ['id' => 1, 'status' => 'draft', 'profile' => ['role' => 'admin']],
            ['id' => 2, 'status' => 'published', 'profile' => ['role' => 'author']],
            ['id' => 3, 'status' => 'draft', 'profile' => ['role' => 'author']],
            ['id' => 4],
        ];

        self::assertSame([
            'draft' => [$items[0], $items[2]],
            'published' => [$items[1]],
        ], Arr::groupBy($items, 'status'));

        self::assertSame([
            'admin' => [$items[0]],
            'author' => [$items[1], $items[2]],
        ], Arr::groupBy($items, 'profile.role'));

        self::assertSame([
            1 => $items[0],
            2 => $items[1],
            3 => $items[2],
            4 => $items[3],
        ], Arr::keyBy($items, 'id'));
    }

    public function testKeyBySkipsItemsWithMissingSelectors(): void
    {
        $items = [
            ['id' => 1, 'name' => 'Ada'],
            ['name' => 'No id'],
            ['id' => 2, 'name' => 'Grace'],
        ];

        self::assertSame([
            1 => $items[0],
            2 => $items[2],
        ], Arr::keyBy($items, 'id'));
    }
}
