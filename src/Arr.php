<?php

declare(strict_types=1);

namespace Phyx;

/**
 * Phyx array façade — a modern, predictable redesign of PHP's native
 * array functions.
 *
 * Every method is `public static`. The class is intentionally agglomerated:
 * each cohesive group of operations lives in a `Phyx\Arr\Handle*` trait
 * (one responsibility per trait) and `Arr` simply composes them. See
 * `docs/Arr.md` for the full convention set.
 */
class Arr
{
    use Arr\HandleAccess;
    use Arr\HandleInspect;
    use Arr\HandleSearch;
    use Arr\HandleTransform;
    use Arr\HandleShape;
    use Arr\HandleGroup;
    use Arr\HandleSort;
    use Arr\HandleSet;
    use Arr\HandleMerge;
    use Arr\HandleRandom;
    use Arr\HandleWalk;
    use Arr\HandleCombine;
}
