<?php

declare(strict_types=1);

namespace Phyx\Tests\Html;

use Phyx\Html;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Html::class)]
final class HandleTagsTest extends TestCase
{
    public function testStripTagsRemovesAllTagsByDefault(): void
    {
        self::assertSame('hi there', Html::stripTags('<p>hi <b>there</b></p>'));
    }

    public function testStripTagsKeepsAllowedTags(): void
    {
        self::assertSame('<p>hi xthere</p>', Html::stripTags('<p>hi <script>x</script><b>there</b></p>', ['p']));
    }

    public function testAllowedTagsBuildsInternalAllowListSafely(): void
    {
        self::assertSame('<p><a><custom-tag><script>', Html::allowedTags(['p', '<a>', 'custom-tag', 'bad tag', 'script>'])) ;
    }

    public function testHasTagsDetectsRealTags(): void
    {
        self::assertTrue(Html::hasTags('hello <strong>world</strong>'));
        self::assertFalse(Html::hasTags('2 < 3 and 4 > 1'));
    }
}
