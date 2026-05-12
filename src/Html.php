<?php

declare(strict_types=1);

namespace Phyx;

/**
 * Provides comprehensive HTML manipulation and escaping utilities.
 *
 * This class serves as a central entry point for HTML-related operations, including
 * character escaping, entity encoding, tag manipulation, and attribute rendering.
 * It ensures safe output by default using UTF-8 encoding and HTML5-compliant
 * escaping strategies across various HTML contexts.
 *
 * @see \Phyx\Html\HandleEscape
 * @see \Phyx\Html\HandleEntities
 * @see \Phyx\Html\HandleTags
 * @see \Phyx\Html\HandleAttributes
 * @see \Phyx\Html\HandleText
 * @see \Phyx\Html\HandleFragments
 * @see \Phyx\Html\HandleTables
 */
class Html
{
    use Html\HandleEscape;
    use Html\HandleEntities;
    use Html\HandleTags;
    use Html\HandleAttributes;
    use Html\HandleText;
    use Html\HandleFragments;
    use Html\HandleTables;
}
