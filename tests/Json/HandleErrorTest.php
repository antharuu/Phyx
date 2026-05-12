<?php

declare(strict_types=1);

namespace Phyx\Tests\Json;

use Phyx\Json;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Json::class)]
final class HandleErrorTest extends TestCase
{
    public function testLastErrorAndMessageExposeNativeJsonState(): void
    {
        json_decode('{');

        self::assertSame(JSON_ERROR_SYNTAX, Json::lastError());
        self::assertSame(json_last_error_msg(), Json::lastErrorMessage());
    }
}
