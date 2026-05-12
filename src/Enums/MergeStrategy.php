<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum MergeStrategy
{
    case Overwrite;
    case Append;
    case Recursive;
}
