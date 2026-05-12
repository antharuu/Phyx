<?php

declare(strict_types=1);

namespace Phyx\Tests\Html;

use InvalidArgumentException;
use Phyx\Html;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Html::class)]
final class HandleFragmentsTest extends TestCase
{
    public function testTagBuildsEscapedElement(): void
    {
        self::assertSame('<a href="/x?y=1&amp;z=2" disabled>Tom &amp; Jerry</a>', Html::tag('a', 'Tom & Jerry', ['href' => '/x?y=1&z=2', 'disabled' => true]));
    }

    public function testVoidTagBuildsElementWithoutClosingTag(): void
    {
        self::assertSame('<input type="checkbox" checked>', Html::voidTag('input', ['type' => 'checkbox', 'checked' => true]));
    }

    public function testCommentEscapesCommentTerminators(): void
    {
        self::assertSame('<!-- safe - - > comment -->', Html::comment('safe --> comment'));
    }

    public function testFragmentsRejectInvalidTagNames(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Html::tag('bad tag', 'x');
    }
}
