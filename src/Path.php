<?php

declare(strict_types=1);

namespace Phyx;

/**
 * Utility class for manipulating filesystem paths.
 *
 * Provides a set of static methods for normalizing, joining, and inspecting
 * paths in a cross-platform manner. Handles both Unix-style (/) and
 * Windows-style (\) paths, including drive letters and UNC paths.
 */
class Path
{
    use Path\HandleJoin;
    use Path\HandleNormalize;
    use Path\HandleInspect;
    use Path\HandleParts;
    use Path\HandleSegments;
    use Path\HandleTransform;
    use Path\HandleResolve;
    use Path\HandleMatch;
}
