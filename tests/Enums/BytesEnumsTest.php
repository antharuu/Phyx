<?php

declare(strict_types=1);

namespace Phyx\Tests\Enums;

use Phyx\Enums\BinaryEncoding;
use Phyx\Enums\ByteOrder;
use Phyx\Enums\Checksum;
use Phyx\Enums\PaddingMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByteOrder::class)]
#[CoversClass(BinaryEncoding::class)]
#[CoversClass(PaddingMode::class)]
#[CoversClass(Checksum::class)]
final class BytesEnumsTest extends TestCase
{
    public function testBytesRelatedEnumsExposeDocumentedCases(): void
    {
        self::assertSame(['BigEndian', 'LittleEndian', 'Machine'], array_map(static fn (ByteOrder $case): string => $case->name, ByteOrder::cases()));
        self::assertSame(['Hex', 'Base64', 'Base64Url'], array_map(static fn (BinaryEncoding $case): string => $case->name, BinaryEncoding::cases()));
        self::assertSame(['Strict', 'Lenient'], array_map(static fn (PaddingMode $case): string => $case->name, PaddingMode::cases()));
        self::assertSame(['Crc32', 'Adler32'], array_map(static fn (Checksum $case): string => $case->name, Checksum::cases()));
    }
}
