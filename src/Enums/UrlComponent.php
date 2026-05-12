<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum UrlComponent
{
    case Scheme;
    case User;
    case Pass;
    case Host;
    case Port;
    case Path;
    case Query;
    case Fragment;
}
