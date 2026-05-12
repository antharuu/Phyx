<?php

declare(strict_types=1);

namespace Phyx\Tests\Path;

use Phyx\Enums\PathStyle;
use Phyx\Path;
use PHPUnit\Framework\TestCase;

final class HandleInspectTest extends TestCase
{
    public function testAbsoluteRelativeAndRootDetection(): void
    {
        self::assertTrue(Path::isAbsolute('/'));
        self::assertTrue(Path::isAbsolute('/tmp/file'));
        self::assertTrue(Path::isAbsolute('C:\\Temp'));
        self::assertTrue(Path::isAbsolute('\\\\server\\share\\dir'));
        self::assertFalse(Path::isAbsolute('C:relative'));
        self::assertTrue(Path::isRelative('foo/bar'));
        self::assertFalse(Path::isRelative('/foo'));
    }

    public function testRootValues(): void
    {
        self::assertTrue(Path::isRoot('/'));
        self::assertTrue(Path::isRoot('C:\\'));
        self::assertTrue(Path::isRoot('\\\\server\\share'));
        self::assertFalse(Path::isRoot('/tmp'));
        self::assertSame('/', Path::root('/tmp'));
        self::assertSame('C:\\', Path::root('C:/Temp'));
        self::assertSame('\\\\server\\share', Path::root('\\\\server\\share\\dir'));
        self::assertNull(Path::root('relative/path'));
    }

    public function testStyleDetection(): void
    {
        self::assertSame(PathStyle::Windows, Path::style('C:\\Temp'));
        self::assertSame(PathStyle::Windows, Path::style('a\\b'));
        self::assertSame(PathStyle::Unix, Path::style('/a/b'));
        self::assertSame(PathStyle::Unix, Path::style(''));
    }
}
