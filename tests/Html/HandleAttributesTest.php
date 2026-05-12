<?php

declare(strict_types=1);

namespace Phyx\Tests\Html;

use InvalidArgumentException;
use Phyx\Html;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Html::class)]
final class HandleAttributesTest extends TestCase
{
    public function testAttributeEscapesNameAndValue(): void
    {
        self::assertSame('title="Tom &amp; &quot;Jerry&quot; &apos;x&apos;"', Html::attribute('title', 'Tom & "Jerry" \'x\''));
    }

    public function testAttributeOmitsNullAndFalseValues(): void
    {
        self::assertSame('', Html::attribute('title', null));
        self::assertSame('', Html::attribute('hidden', false));
    }

    public function testAttributeRendersTrueAsBooleanAttribute(): void
    {
        self::assertSame('disabled', Html::attribute('disabled', true));
    }

    public function testBooleanAttribute(): void
    {
        self::assertSame('checked', Html::booleanAttribute('checked', true));
        self::assertSame('', Html::booleanAttribute('checked', false));
    }

    public function testAttributesOmitNullAndJoinNonEmptyAttributes(): void
    {
        self::assertSame('id="main" disabled data-count="3"', Html::attributes(['id' => 'main', 'title' => null, 'disabled' => true, 'data-count' => 3]));
    }

    public function testAttributesRejectInvalidNames(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Html::attribute('bad name', 'x');
    }
}
