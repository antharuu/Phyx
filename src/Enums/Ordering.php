<?php

declare(strict_types=1);

namespace Phyx\Enums;

/**
 * Selects the algorithm used to order two strings.
 *
 * Used as the last argument of {@see \Phyx\Str::compare()} (and any future
 * Phyx method that needs to choose between lexicographic, human-friendly or
 * locale-aware ordering). Replaces the PHP zoo of `strcmp` / `strnatcmp` /
 * `strcoll` (plus their `*case*` variants) with a single signature whose
 * behaviour is selected through this enum combined with
 * {@see CaseSensitivity}.
 *
 * @see \Phyx\Str::compare()
 */
enum Ordering
{
    /**
     * Byte-by-byte lexicographic comparison.
     *
     * Equivalent role: PHP's `strcmp()` / `strcasecmp()`. The fastest mode,
     * but `'item2'` sorts before `'item10'` because `'2'` > `'1'`.
     */
    case Binary;

    /**
     * Human-friendly "natural" ordering — numeric runs are compared as numbers.
     *
     * Equivalent role: PHP's `strnatcmp()` / `strnatcasecmp()`. With this mode
     * `'item2'` sorts before `'item10'`, which matches what a human reader
     * intuitively expects.
     */
    case Natural;

    /**
     * Comparison driven by the current locale.
     *
     * Equivalent role: PHP's `strcoll()`. Useful when accented letters must
     * be sorted according to the conventions of a specific language. Behaviour
     * depends on the LC_COLLATE setting of the runtime.
     */
    case Locale;
}
