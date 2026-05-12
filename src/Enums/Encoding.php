<?php

declare(strict_types=1);

namespace Phyx\Enums;

/**
 * Character encoding selector for Phyx string operations.
 *
 * Used as the trailing argument of every Phyx method that internally calls
 * a `mb_*` function. Replaces the loose `string $encoding = 'UTF-8'`
 * convention used by PHP's mbstring API with a type-safe enum, eliminating
 * the typo class (`'utf8'`, `'UTF8'`, `'Utf-8'`, …) and making the supported
 * set explicit.
 *
 * The cases cover the encodings actually used in modern PHP applications
 * (Unicode family, ASCII, the Latin and Windows code pages most often
 * encountered when ingesting legacy data, and the four most common CJK
 * encodings). They map one-to-one to the canonical names accepted by
 * mbstring — use {@see Encoding::value()} (i.e. `$encoding->value`) when
 * you need the raw string identifier.
 *
 * @see \Phyx\Str
 */
enum Encoding: string
{
    /** Unicode, variable-width 1–4 bytes. */
    case Utf8 = 'UTF-8';

    /** Unicode, 2 or 4 bytes, big-endian byte order. */
    case Utf16Be = 'UTF-16BE';

    /** Unicode, 2 or 4 bytes, little-endian byte order. */
    case Utf16Le = 'UTF-16LE';

    /** Unicode, 4 bytes, big-endian byte order. */
    case Utf32Be = 'UTF-32BE';

    /** Unicode, 4 bytes, little-endian byte order. */
    case Utf32Le = 'UTF-32LE';

    /** 7-bit ASCII. */
    case Ascii = 'ASCII';

    /** Latin-1 (Western European). */
    case Iso88591 = 'ISO-8859-1';

    /** Latin-2 (Central European). */
    case Iso88592 = 'ISO-8859-2';

    /** Latin/Cyrillic. */
    case Iso88595 = 'ISO-8859-5';

    /** Latin/Arabic. */
    case Iso88596 = 'ISO-8859-6';

    /** Latin/Greek. */
    case Iso88597 = 'ISO-8859-7';

    /** Latin/Hebrew. */
    case Iso88598 = 'ISO-8859-8';

    /** Latin-9 — Latin-1 with the euro sign and a few French additions. */
    case Iso885915 = 'ISO-8859-15';

    /** Windows Cyrillic code page (Russian, Bulgarian, …). */
    case Windows1251 = 'Windows-1251';

    /** Windows Western European (Latin-1 superset, default on Western Windows). */
    case Windows1252 = 'Windows-1252';

    /** Windows Turkish code page. */
    case Windows1254 = 'Windows-1254';

    /** Japanese, Shift-JIS encoding. */
    case ShiftJis = 'SJIS';

    /** Japanese, EUC-JP encoding. */
    case EucJp = 'EUC-JP';

    /** Traditional Chinese, Big5 encoding. */
    case Big5 = 'BIG-5';

    /** Simplified/Traditional Chinese, GB 18030 encoding. */
    case Gb18030 = 'GB18030';
}
