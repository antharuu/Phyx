<?php

declare(strict_types=1);

namespace Phyx\Tests\Path;

use Phyx\Path;
use PHPUnit\Framework\TestCase;

final class HandleSegmentsTest extends TestCase
{
    public function testSegmentsIgnoreRootsAndEmptySeparators(): void
    {
        self::assertSame(['tmp', 'foo', 'bar.txt'], Path::segments('/tmp//foo/bar.txt'));
        self::assertSame(['Temp', 'file.txt'], Path::segments('C:\\Temp\\file.txt'));
        self::assertSame(['dir'], Path::segments('\\\\server\\share\\dir'));
        self::assertSame([], Path::segments('/'));
        self::assertSame([], Path::segments(''));
    }

    public function testParentAncestorsAndLastSegment(): void
    {
        self::assertSame('/tmp/foo', Path::parent('/tmp/foo/bar.txt'));
        self::assertSame('/', Path::parent('/tmp'));
        self::assertNull(Path::parent('/'));
        self::assertNull(Path::parent(''));
        self::assertSame(['/tmp/foo', '/tmp', '/'], Path::ancestors('/tmp/foo/bar.txt'));
        self::assertSame(['a/b', 'a'], Path::ancestors('a/b/c'));
        self::assertSame('bar.txt', Path::lastSegment('/tmp/foo/bar.txt'));
        self::assertNull(Path::lastSegment('/'));
    }
}
