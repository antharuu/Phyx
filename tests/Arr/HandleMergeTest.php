<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class)]
final class HandleMergeTest extends TestCase
{
    public function testMergeReplaceAppendAndPrependReturnNewArrays(): void
    {
        $array = ['a' => 1];

        self::assertSame(['a' => 1, 'b' => 2, 'c' => 3], Arr::merge($array, ['b' => 2], ['c' => 3]));
        self::assertSame(['a' => [1, 2]], Arr::mergeRecursive(['a' => 1], ['a' => 2]));
        self::assertSame(['a' => 9, 'b' => 2], Arr::replace($array, ['a' => 9, 'b' => 2]));
        self::assertSame(['a' => ['x' => 1, 'y' => 2]], Arr::replaceRecursive(['a' => ['x' => 1]], ['a' => ['y' => 2]]));
        self::assertSame(['a' => 1, 0 => 2], Arr::append($array, 2));
        self::assertSame([0 => 0, 'a' => 1], Arr::prepend($array, 0));
        self::assertSame(['z' => 0, 'a' => 1], Arr::prepend($array, 0, 'z'));
        self::assertSame(['a' => 1], $array);
    }
}
