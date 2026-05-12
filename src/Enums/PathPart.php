<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum PathPart
{
    case Dirname;
    case Basename;
    case Filename;
    case Extension;
}
