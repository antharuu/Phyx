<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum PathRoot
{
    case None;
    case Unix;
    case WindowsDrive;
    case Unc;
}
