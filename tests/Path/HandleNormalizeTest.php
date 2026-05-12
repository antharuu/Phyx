<?php

declare(strict_types=1);

namespace Phyx\Tests\Path;

use Phyx\Enums\PathStyle;
use Phyx\Path;
use PHPUnit\Framework\TestCase;

final class HandleNormalizeTest extends TestCase
{
    public function testNormalizeCollapsesDotsSeparatorsAndTrailingSeparators(): void
    {
        self::assertSame('/foo/baz', Path::normalize('/foo//bar/../baz/'));
        self::assertSame('../baz', Path::normalize('foo/../../baz'));
        self::assertSame('/', Path::normalize('/'));
        self::assertSame('', Path::normalize(''));
    }

    public function testNormalizeWithWindowsStyle(): void
    {
        self::assertSame('C:\\foo\\baz', Path::normalize('C:/foo//bar/../baz', PathStyle::Windows));
        self::assertSame('\\\\server\\share\\dir', Path::normalize('\\\\server/share//dir', PathStyle::Windows));
    }

    public function testSeparatorHelpers(): void
    {
        self::assertSame('a/b/c', Path::normalizeSeparators('a\\b//c', PathStyle::Unix));
        self::assertSame('//server/share', Path::normalizeSeparators('\\\\server\\share', PathStyle::Unix));
        self::assertSame('a\\b\\c', Path::normalizeSeparators('a/b//c', PathStyle::Windows));
        self::assertSame('/foo', Path::removeTrailingSeparator('/foo///'));
        self::assertSame('/', Path::removeTrailingSeparator('/'));
        self::assertSame(DIRECTORY_SEPARATOR, Path::ensureTrailingSeparator(''));
        self::assertSame('/foo/', Path::ensureTrailingSeparator('/foo'));
        self::assertSame('/', Path::ensureTrailingSeparator('/'));
    }
}
