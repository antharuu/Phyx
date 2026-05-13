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

    public function testUniqueByKeepsFirstItemForEachSelectedValue(): void
    {
        $users = [
            'first' => ['email' => 'ada@example.test', 'profile' => ['team' => 'core']],
            'second' => ['email' => 'grace@example.test', 'profile' => ['team' => 'core']],
            'duplicate' => ['email' => 'ada@example.test', 'profile' => ['team' => 'docs']],
            'missing' => ['name' => 'No email'],
            'missing-again' => ['name' => 'Still no email'],
        ];

        self::assertSame([
            'first' => $users['first'],
            'second' => $users['second'],
            'missing' => $users['missing'],
        ], Arr::uniqueBy($users, 'email'));

        self::assertSame([
            'first' => $users['first'],
            'duplicate' => $users['duplicate'],
            'missing' => $users['missing'],
        ], Arr::uniqueBy($users, 'profile.team'));

        self::assertSame([
            'first' => $users['first'],
            'second' => $users['second'],
            'missing' => $users['missing'],
        ], Arr::uniqueBy($users, static fn (array $user): mixed => $user['email'] ?? null));
    }

    public function testUniqueByRejectsInvalidSelectors(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Array selector must be a callable, string, or integer.');

        /** @var mixed $selector */
        $selector = [];

        Arr::uniqueBy([['id' => 1]], $selector);
    }

    public function testDiffIntersectAndUnionPreservePredictableNativeSemantics(): void
    {
        self::assertSame(['a' => 1], Arr::diff(['a' => 1, 'b' => 2], [2]));
        self::assertSame(['b' => 2], Arr::intersect(['a' => 1, 'b' => 2], [2, 3]));
        self::assertSame(['a' => 1, 'b' => 2], Arr::union(['a' => 1], ['a' => 9, 'b' => 2]));
    }
}
