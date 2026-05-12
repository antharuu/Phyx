<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ValueError;

#[CoversClass(Arr::class)]
final class HandleRandomTest extends TestCase
{
    public function testRandomSampleAndShuffleReturnValuesFromInput(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];

        self::assertContains(Arr::random($array), [1, 2, 3]);

        $sample = Arr::sample($array, 2);
        self::assertCount(2, $sample);
        self::assertSame([], array_diff_key($sample, $array));
        self::assertSame([], Arr::sample($array, 0));

        $shuffled = Arr::shuffle($array);
        sort($shuffled);
        self::assertSame([1, 2, 3], $shuffled);
    }

    public function testRandomAndSampleValidateImpossibleRequests(): void
    {
        $this->expectException(ValueError::class);
        Arr::random([]);
    }

    public function testSampleRejectsOutOfRangeCount(): void
    {
        $this->expectException(ValueError::class);
        Arr::sample([1], 2);
    }
}
