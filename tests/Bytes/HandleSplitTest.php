<?php

declare(strict_types=1);

namespace Phyx\Tests\Bytes;

use Phyx\Bytes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ValueError;

#[CoversClass(Bytes::class)]
final class HandleSplitTest extends TestCase
{
    public function testSplitReturnsByteChunks(): void
    {
        self::assertSame(['ab', 'cd', 'e'], Bytes::split('abcde', 2));
        self::assertSame([], Bytes::split('', 2));
        self::assertSame(["\xC3", "\xA9"], Bytes::split('é', 1));
        self::assertSame(["a\0", 'b'], Bytes::split("a\0b", 2));
    }

    public function testSplitRejectsNonPositiveLength(): void
    {
        $this->expectException(ValueError::class);

        Bytes::split('abc', 0);
    }

    public function testChunkUsesByteLengthAndKeepsTrailingSeparator(): void
    {
        self::assertSame('ab|cd|e|', Bytes::chunk('abcde', 2, '|'));
        self::assertSame('', Bytes::chunk('', 2, '|'));
        self::assertSame("\xC3|\xA9|", Bytes::chunk('é', 1, '|'));
    }

    public function testChunkRejectsNonPositiveLength(): void
    {
        $this->expectException(ValueError::class);

        Bytes::chunk('abc', 0);
    }
}
