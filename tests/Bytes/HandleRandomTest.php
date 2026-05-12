<?php

declare(strict_types=1);

namespace Phyx\Tests\Bytes;

use Phyx\Bytes;
use Phyx\Enums\Checksum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ValueError;

#[CoversClass(Bytes::class)]
final class HandleRandomTest extends TestCase
{
    public function testRandomReturnsRequestedByteLength(): void
    {
        self::assertSame('', Bytes::random(0));
        self::assertSame(16, Bytes::length(Bytes::random(16)));
    }

    public function testRandomRejectsNegativeLength(): void
    {
        $this->expectException(ValueError::class);

        Bytes::random(-1);
    }

    public function testCrc32ReturnsUnsignedNativeCrc(): void
    {
        self::assertSame((int) sprintf('%u', crc32('abc')), Bytes::crc32('abc'));
        self::assertSame((int) sprintf('%u', crc32("\0")), Bytes::checksum("\0", Checksum::Crc32));
    }

    public function testChecksumSupportsAdler32(): void
    {
        self::assertSame(hash('adler32', 'abc'), Bytes::checksum('abc', Checksum::Adler32));
    }
}
