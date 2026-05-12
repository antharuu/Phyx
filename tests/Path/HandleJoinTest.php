<?php

declare(strict_types=1);

namespace Phyx\Tests\Path;

use Phyx\Path;
use PHPUnit\Framework\TestCase;

final class HandleJoinTest extends TestCase
{
    public function testJoinSkipsEmptyPartsAndNormalizesSegments(): void
    {
        self::assertSame('/var/log/app.log', Path::join('/var', '', 'log', './app.log'));
        self::assertSame('', Path::join('', ''));
    }

    public function testJoinPreservesWindowsAndUncRoots(): void
    {
        self::assertSame('C:\\Temp\\file.txt', Path::join('C:\\', 'Temp', 'file.txt'));
        self::assertSame('\\\\server\\share\\dir', Path::join('\\\\server\\share', 'dir'));
    }

    public function testAppendAndPrepend(): void
    {
        self::assertSame('base/child/file.txt', Path::append('base', 'child', 'file.txt'));
        self::assertSame('/root/base/child', Path::prepend('base/child', '/root'));
    }
}
