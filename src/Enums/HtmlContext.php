<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum HtmlContext
{
    case Text;
    case Attribute;
    case Url;
    case Raw;
}
