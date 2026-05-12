<?php

declare(strict_types=1);

namespace Phyx\Tests;

use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    public function testComposerManifestExists(): void
    {
        self::assertFileExists(dirname(__DIR__) . '/composer.json');
    }
}
