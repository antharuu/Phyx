<?php

declare(strict_types=1);

namespace Phyx\Tests\Arr;

use Phyx\Arr;
use Phyx\Enums\SortDirection;
use Phyx\Enums\SortMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class)]
final class HandleSortTest extends TestCase
{
    public function testSortCanResetOrPreserveKeys(): void
    {
        self::assertSame([1, 2, 3], Arr::sort(['b' => 2, 'a' => 1, 'c' => 3]));
        self::assertSame(['c' => 3, 'b' => 2, 'a' => 1], Arr::sort(['b' => 2, 'a' => 1, 'c' => 3], SortDirection::Descending, SortMode::Regular, true));
    }

    public function testSortKeysSortByAndReverse(): void
    {
        self::assertSame(['a' => 1, 'b' => 2], Arr::sortKeys(['b' => 2, 'a' => 1]));
        self::assertSame(['b' => 2, 'a' => 1], Arr::sortKeys(['a' => 1, 'b' => 2], SortDirection::Descending));
        self::assertSame(['a' => ['rank' => 1], 'b' => ['rank' => 2]], Arr::sortBy(['b' => ['rank' => 2], 'a' => ['rank' => 1]], static fn (array $row): int => $row['rank']));
        self::assertSame([3, 2, 1], Arr::reverse([1, 2, 3]));
        self::assertSame(['c' => 3, 'b' => 2, 'a' => 1], Arr::reverse(['a' => 1, 'b' => 2, 'c' => 3], true));
    }

    public function testSortBySupportsNestedSelectors(): void
    {
        $rows = [
            'later' => ['meta' => ['createdAt' => '2024-02-01']],
            'missing' => ['meta' => []],
            'earlier' => ['meta' => ['createdAt' => '2024-01-01']],
        ];

        self::assertSame([
            'missing' => $rows['missing'],
            'earlier' => $rows['earlier'],
            'later' => $rows['later'],
        ], Arr::sortBy($rows, 'meta.createdAt'));

        self::assertSame([
            'later' => $rows['later'],
            'earlier' => $rows['earlier'],
            'missing' => $rows['missing'],
        ], Arr::sortBy($rows, 'meta.createdAt', SortDirection::Descending));
    }

    public function testSortModeMapsEverySupportedNativeFlag(): void
    {
        self::assertSame(['img1', 'img2', 'img10'], Arr::sort(['img10', 'img2', 'img1'], SortDirection::Ascending, SortMode::Natural));
        self::assertSame(['10', '2'], Arr::sort(['2', '10'], SortDirection::Descending, SortMode::Numeric));
        self::assertSame(['10', '2'], Arr::sort(['2', '10'], SortDirection::Ascending, SortMode::String));
    }
}
