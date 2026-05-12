<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class)]
final class HandleCombineTest extends TestCase
{
    public function testCombineZipPairAndTranspose(): void
    {
        self::assertSame(['a' => 1, 'b' => 2], Arr::combine(['a', 'b'], [1, 2]));
        self::assertSame([[1, 'a'], [2, 'b']], Arr::zip([1, 2, 3], ['a', 'b']));
        self::assertSame([], Arr::zip());
        self::assertSame(['name', 'Ada'], Arr::pair(['name' => 'Ada', 'age' => 36]));
        self::assertSame([null, null], Arr::pair([]));
        self::assertSame([[1, 3], [2, 4]], Arr::transpose([[1, 2], [3, 4]]));
        self::assertSame([[1, 3, 5]], Arr::transpose([[1, 2], [3, 4], [5]]));
        self::assertSame([], Arr::transpose([]));
    }
}
