<?php

declare(strict_types=1);

namespace Phyx\Tests\Polyfills;

use Phyx\Polyfills\JsonValidate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ValueError;

#[CoversClass(JsonValidate::class)]
final class JsonValidateTest extends TestCase
{
    public function testValidateReturnsBooleanInsteadOfThrowingForJsonSyntax(): void
    {
        self::assertTrue(JsonValidate::validate('{"ok":true}'));
        self::assertFalse(JsonValidate::validate('{'));
    }

    public function testValidateRejectsUnsupportedFlags(): void
    {
        $this->expectException(ValueError::class);

        JsonValidate::validate('{"ok":true}', flags: JSON_INVALID_UTF8_IGNORE);
    }

    public function testValidateRejectsInvalidDepth(): void
    {
        $this->expectException(ValueError::class);

        JsonValidate::validate('{"ok":true}', 0);
    }
}
