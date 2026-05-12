<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class)]
final class HandleWalkTest extends TestCase
{
    public function testDotAndUndotConvertNestedPaths(): void
    {
        self::assertSame(['user.name' => 'Ada', 'user.roles.0' => 'admin', 'empty' => []], Arr::dot(['user' => ['name' => 'Ada', 'roles' => ['admin']], 'empty' => []]));
        self::assertSame(['user' => ['name' => 'Ada']], Arr::undot(['user.name' => 'Ada']));
        self::assertSame(['user' => ['name' => 'Ada']], Arr::undot(['' => 'ignored', 'user/name' => 'Ada'], '/'));
    }

    public function testWalkAndWalkRecursiveUseValueThenKeyWithoutMutatingInput(): void
    {
        $array = ['a' => 1, 'b' => ['c' => 2]];

        self::assertSame(['a' => 'a:1', 'b' => 'b:Array'], Arr::walk($array, static fn (mixed $value, string $key): string => $key . ':' . (is_array($value) ? 'Array' : (string) $value)));
        self::assertSame(['a' => 'a:1', 'b' => ['c' => 'c:2']], Arr::walkRecursive($array, static fn (int $value, string $key): string => $key . ':' . $value));
        self::assertSame(['a' => 1, 'b' => ['c' => 2]], $array);
    }

    public function testPathSeparatorsCannotBeEmpty(): void
    {
        foreach (['dot', 'undot'] as $method) {
            try {
                $method === 'dot'
                    ? Arr::dot(['a' => 1], '')
                    : Arr::undot(['a' => 1], '');
                self::fail($method . ' should reject an empty separator.');
            } catch (\InvalidArgumentException) {
                self::addToAssertionCount(1);
            }
        }
    }
}
