<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class)]
final class HandleTransformTest extends TestCase
{
    public function testMapCallbacksReceiveValueThenKey(): void
    {
        self::assertSame(
            ['a' => 'a:2', 'b' => 'b:4'],
            Arr::map(['a' => 1, 'b' => 2], static fn (int $value, string $key): string => $key . ':' . ($value * 2)),
        );
    }

    public function testMapKeysAndMapWithKeysCanChangeKeys(): void
    {
        self::assertSame(['A' => 1, 'B' => 2], Arr::mapKeys(['a' => 1, 'b' => 2], static fn (int $_value, string $key): string => strtoupper($key)));
        self::assertSame(['a1' => 10, 'b2' => 20], Arr::mapWithKeys(['a' => 1, 'b' => 2], static fn (int $value, string $key): array => [$key . $value => $value * 10]));
    }

    public function testFilterRejectAndReduceUseValueThenKey(): void
    {
        $array = ['a' => 0, 'b' => 2, 'c' => 3];

        self::assertSame(['b' => 2, 'c' => 3], Arr::filter($array));
        self::assertSame(['b' => 2], Arr::filter($array, static fn (int $value, string $key): bool => $value % 2 === 0 && $key !== 'a'));
        self::assertSame(['a' => 0, 'c' => 3], Arr::reject($array, static fn (int $value): bool => $value === 2));
        self::assertSame('a0,b2,c3', Arr::reduce($array, static fn (string $carry, int $value, string $key): string => trim($carry . ',' . $key . $value, ','), ''));
    }
}
