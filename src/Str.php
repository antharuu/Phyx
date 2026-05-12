<?php

declare(strict_types=1);

namespace Phyx;

/**
 * Phyx string façade — a modern, multibyte-safe redesign of PHP's native
 * string functions.
 *
 * Every method is `public static`. The class is intentionally agglomerated:
 * each cohesive group of operations lives in a `Phyx\Str\Handle*` trait
 * (one responsibility per trait) and `Str` simply composes them. See
 * `docs/Str.md` for the full convention set.
 */
class Str
{
    use Str\HandleCase;
    use Str\HandleCompare;
    use Str\HandleEncode;
    use Str\HandleEscape;
    use Str\HandleFormat;
    use Str\HandleHash;
    use Str\HandleHtml;
    use Str\HandleLength;
    use Str\HandleReplace;
    use Str\HandleSearch;
    use Str\HandleShape;
    use Str\HandleSlice;
    use Str\HandleSplit;
    use Str\HandleTrim;
}
