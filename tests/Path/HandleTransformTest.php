<?php

declare(strict_types=1);

namespace Phyx\Tests\Path;

use Phyx\Path;
use PHPUnit\Framework\TestCase;

final class HandleTransformTest extends TestCase
{
    public function testExtensionTransformations(): void
    {
        self::assertSame('/tmp/file.md', Path::withExtension('/tmp/file.txt', 'md'));
        self::assertSame('/tmp/file.md', Path::withExtension('/tmp/file', '.md'));
        self::assertSame('/tmp/file.tar', Path::withoutExtension('/tmp/file.tar.gz'));
        self::assertSame('/tmp/file', Path::withoutExtension('/tmp/file'));
    }

    public function testSeparatorTransformations(): void
    {
        self::assertSame('C:/Temp/file.txt', Path::toUnix('C:\\Temp\\file.txt'));
        self::assertSame('a\\b\\c', Path::toWindows('a/b/c'));
    }

    public function testRelative(): void
    {
        self::assertSame('bar/baz.txt', Path::relative('/tmp/foo/bar/baz.txt', '/tmp/foo'));
        self::assertSame('../other/file.txt', Path::relative('/tmp/other/file.txt', '/tmp/foo'));
        self::assertSame('', Path::relative('/tmp/foo', '/tmp/foo'));
        self::assertSame('D:\\data', Path::relative('D:\\data', 'C:\\base'));
    }
}
