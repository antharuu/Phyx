<?php

declare(strict_types=1);

namespace Phyx\Tests\Enums;

use Phyx\Enums\CaseSensitivity;
use Phyx\Enums\Encoding;
use Phyx\Enums\Ordering;
use Phyx\Enums\Side;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CaseSensitivity::class)]
#[CoversClass(Side::class)]
#[CoversClass(Ordering::class)]
#[CoversClass(Encoding::class)]
final class EnumsTest extends TestCase
{
    public function testCaseSensitivityCases(): void
    {
        self::assertSame(
            ['Sensitive', 'Insensitive'],
            array_map(static fn (CaseSensitivity $c): string => $c->name, CaseSensitivity::cases()),
        );
    }

    public function testSideCases(): void
    {
        self::assertSame(
            ['Start', 'End', 'Both'],
            array_map(static fn (Side $s): string => $s->name, Side::cases()),
        );
    }

    public function testOrderingCases(): void
    {
        self::assertSame(
            ['Binary', 'Natural', 'Locale'],
            array_map(static fn (Ordering $o): string => $o->name, Ordering::cases()),
        );
    }

    public function testEncodingIsBackedByCanonicalMbstringName(): void
    {
        self::assertSame('UTF-8', Encoding::Utf8->value);
        self::assertSame('UTF-16BE', Encoding::Utf16Be->value);
        self::assertSame('UTF-16LE', Encoding::Utf16Le->value);
        self::assertSame('UTF-32BE', Encoding::Utf32Be->value);
        self::assertSame('UTF-32LE', Encoding::Utf32Le->value);
        self::assertSame('ASCII', Encoding::Ascii->value);
        self::assertSame('ISO-8859-1', Encoding::Iso88591->value);
        self::assertSame('ISO-8859-15', Encoding::Iso885915->value);
        self::assertSame('Windows-1251', Encoding::Windows1251->value);
        self::assertSame('Windows-1252', Encoding::Windows1252->value);
        self::assertSame('Windows-1254', Encoding::Windows1254->value);
        self::assertSame('SJIS', Encoding::ShiftJis->value);
        self::assertSame('EUC-JP', Encoding::EucJp->value);
        self::assertSame('BIG-5', Encoding::Big5->value);
        self::assertSame('GB18030', Encoding::Gb18030->value);
    }

    public function testEncodingCasesAreAllAcceptedByMbstring(): void
    {
        $supported = mb_list_encodings();

        foreach (Encoding::cases() as $encoding) {
            self::assertContains(
                $encoding->value,
                $supported,
                "Encoding {$encoding->name} ('{$encoding->value}') must be recognised by mbstring",
            );
        }
    }
}
