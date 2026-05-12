<?php

declare(strict_types=1);

namespace Phyx\Enums;

/**
 * Selects which side(s) of a string an operation targets.
 *
 * Used as the last argument of methods that operate on string borders such
 * as {@see \Phyx\Str::trim()} and {@see \Phyx\Str::pad()}. The Phyx API
 * deliberately collapses PHP's family of side-specific functions
 * (`ltrim` / `rtrim` / `trim`, `STR_PAD_LEFT` / `STR_PAD_RIGHT` / `STR_PAD_BOTH`)
 * into a single method whose behaviour is selected through this enum.
 *
 * @see \Phyx\Str
 */
enum Side
{
    /**
     * Target only the beginning of the string (the left side, index 0).
     *
     * Equivalent role: PHP's `ltrim()` for trimming, `STR_PAD_LEFT` for padding.
     */
    case Start;

    /**
     * Target only the end of the string (the right side, after the last character).
     *
     * Equivalent role: PHP's `rtrim()` for trimming, `STR_PAD_RIGHT` for padding.
     */
    case End;

    /**
     * Target both the beginning and the end of the string.
     *
     * Equivalent role: PHP's `trim()` for trimming, `STR_PAD_BOTH` for padding.
     */
    case Both;
}
