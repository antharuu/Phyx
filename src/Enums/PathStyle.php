<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum PathStyle
{
    case Unix;
    case Windows;
    case Native;
}
