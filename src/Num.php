<?php

declare(strict_types=1);

namespace Phyx;

/**
 * Static facade for numeric predicates, arithmetic, conversion and formatting.
 */
class Num
{
    use Num\HandleAggregate;
    use Num\HandleArithmetic;
    use Num\HandleCheck;
    use Num\HandleConvert;
    use Num\HandleFormat;
    use Num\HandlePower;
    use Num\HandleRange;
    use Num\HandleRound;
    use Num\HandleTrig;
}
