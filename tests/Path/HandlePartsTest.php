<?php

declare(strict_types=1);

namespace Phyx\Tests\Path;

use Phyx\Enums\PathPart;
use Phyx\Path;
use PHPUnit\Framework\TestCase;

final class HandlePartsTest extends TestCase
{
    public function testInfoAndSelectedParts(): void
    {
        self::assertSame([
            'dirname' => '/tmp/archive',
            'basename' => 'file.tar.gz',
            'extension' => 'gz',
            'filename' => 'file.tar',
        ], Path::info('/tmp/archive/file.tar.gz'));
        self::assertSame('file.tar.gz', Path::info('/tmp/archive/file.tar.gz', PathPart::Basename));
        self::assertSame('gz', Path::info('/tmp/archive/file.tar.gz', PathPart::Extension));
        self::assertNull(Path::info('/tmp/file', PathPart::Extension));
    }

    public function testDirnameBasenameFilenameAndExtension(): void
    {
        self::assertSame('/tmp', Path::dirname('/tmp/file.txt'));
        self::assertSame('/', Path::dirname('/tmp/file.txt', 2));
        self::assertSame('file', Path::basename('/tmp/file.txt', '.txt'));
        self::assertSame('file.tar', Path::filename('/tmp/file.tar.gz'));
        self::assertSame('gz', Path::extension('/tmp/file.tar.gz'));
        self::assertNull(Path::extension('/tmp/file'));
        self::assertSame('.gitignore', Path::filename('/tmp/.gitignore'));
        self::assertNull(Path::extension('/tmp/.gitignore'));
    }
}
