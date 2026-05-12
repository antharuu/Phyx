<?php

declare(strict_types=1);

namespace Phyx\Enums;

/**
 * Controls how Phyx string operations match letter case.
 *
 * Used as the last argument of methods that accept an optional case mode
 * (see {@see \Phyx\Str::contains()}, {@see \Phyx\Str::indexOf()},
 * {@see \Phyx\Str::compare()}, …). The Phyx API never exposes a `*I`/`*Ci`
 * suffix variant — case sensitivity is always selected through this enum.
 *
 * @see \Phyx\Str
 */
enum CaseSensitivity
{
    /**
     * Match letter case exactly. `'a'` and `'A'` are different.
     *
     * Mirrors PHP's native sensitive functions (`str_contains`, `strpos`,
     * `strcmp`, …).
     */
    case Sensitive;

    /**
     * Ignore letter case during matching. `'a'` and `'A'` are equivalent.
     *
     * Implementations use multibyte-aware helpers (`mb_stripos`, `mb_strtolower`,
     * …) so that accented or non-ASCII letters fold consistently with their
     * UTF-8 case mapping.
     */
    case Insensitive;
}
