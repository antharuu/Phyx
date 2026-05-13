<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class)]
final class HandleAccessTest extends TestCase
{
    public function testDirectAccessDoesNotMutate(): void
    {
        $array = ['a' => null, 'b' => 2];

        self::assertNull(Arr::get($array, 'a', 'x'));
        self::assertSame('x', Arr::get($array, 'missing', 'x'));
        self::assertTrue(Arr::hasKey($array, 'a'));
        self::assertTrue(Arr::containsKey($array, 'a'));
        self::assertSame(['a' => null, 'b' => 2, 'c' => 3], Arr::set($array, 'c', 3));
        self::assertSame(['a' => null], Arr::forget($array, 'b'));
        self::assertSame(['a' => null, 'b' => 2], $array);
    }

    public function testOnlyKeepsRequestedExistingKeysInRequestedOrder(): void
    {
        $array = ['id' => 1, 'name' => 'Ada', 'email' => 'ada@example.test', 'password' => 'secret'];
        $original = $array;

        self::assertSame(['name' => 'Ada', 'id' => 1], Arr::only($array, ['name', 'missing', 'id']));
        self::assertSame([], Arr::only($array, []));
        self::assertSame($original, $array);
    }

    public function testExceptRemovesRequestedKeysAndPreservesRemainingOrder(): void
    {
        $array = ['id' => 1, 'name' => 'Ada', 'password' => 'secret', 'remember_token' => null];
        $original = $array;

        self::assertSame(['id' => 1, 'name' => 'Ada'], Arr::except($array, ['password', 'remember_token', 'missing']));
        self::assertSame($array, Arr::except($array, []));
        self::assertSame($original, $array);
    }

    public function testPathAccessDoesNotMutate(): void
    {
        $array = ['user' => ['name' => null, 'roles' => ['admin']]];

        self::assertSame('fallback', Arr::getPath($array, '', 'fallback'));
        self::assertNull(Arr::getPath($array, 'user.name', 'x'));
        self::assertSame('x', Arr::getPath($array, 'user.email', 'x'));
        self::assertTrue(Arr::hasPath($array, 'user.roles.0'));
        self::assertFalse(Arr::hasPath($array, 'user.roles.1'));
        self::assertSame(['user' => ['name' => null, 'roles' => ['admin'], 'email' => 'a@b.test']], Arr::setPath($array, 'user.email', 'a@b.test'));
        self::assertSame($array, Arr::setPath($array, '', 'ignored'));
        self::assertSame(['user' => ['name' => null]], Arr::forgetPath($array, 'user.roles'));
        self::assertSame($array, Arr::forgetPath($array, 'user.missing.value'));
        self::assertSame($array, Arr::forgetPath($array, ''));
        self::assertFalse(Arr::hasPath($array, ''));
        self::assertSame(['a' => ['b' => 1]], Arr::setPath([], 'a/b', 1, '/'));
    }

    public function testPathSeparatorCannotBeEmpty(): void
    {
        foreach (['getPath', 'setPath', 'forgetPath', 'hasPath'] as $method) {
            try {
                match ($method) {
                    'getPath' => Arr::getPath([], 'a', null, ''),
                    'setPath' => Arr::setPath([], 'a', 1, ''),
                    'forgetPath' => Arr::forgetPath([], 'a', ''),
                    'hasPath' => Arr::hasPath([], 'a', ''),
                };
                self::fail($method . ' should reject an empty separator.');
            } catch (\InvalidArgumentException) {
                self::addToAssertionCount(1);
            }
        }
    }
}
