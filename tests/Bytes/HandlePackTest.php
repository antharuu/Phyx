<?php

declare(strict_types=1);

namespace Phyx\Tests\Bytes;

use Phyx\Bytes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Bytes::class)]
final class HandlePackTest extends TestCase
{
    public function testPackAndUnpackDelegateWithoutFalseReturns(): void
    {
        $bytes = Bytes::pack('Cn', 1, 258);

        self::assertSame("\x01\x01\x02", $bytes);
        self::assertSame(['first' => 1, 'second' => 258], Bytes::unpack('Cfirst/nsecond', $bytes));
    }

    public function testUnpackCanReturnNumericKeys(): void
    {
        self::assertSame([1 => 65, 2 => 66], Bytes::unpack('C*', 'AB'));
    }

    public function testUnpackThrowsWhenNativeUnpackFails(): void
    {
        set_error_handler(static fn (): bool => true);

        try {
            $this->expectException(\ValueError::class);

            Bytes::unpack('C', '');
        } finally {
            restore_error_handler();
        }
    }
}
