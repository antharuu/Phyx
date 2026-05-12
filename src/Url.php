<?php

declare(strict_types=1);

namespace Phyx;

/**
 * Provides a comprehensive set of static methods for URL manipulation.
 *
 * Implements URI/URL parsing, building, normalization, validation, and encoding
 * according to RFC 3986 and RFC 6454. This class acts as a facade for various
 * URL-related operations.
 *
 * @see Url\HandleParse
 * @see Url\HandleParts
 * @see Url\HandleBuild
 * @see Url\HandleQuery
 * @see Url\HandleEncode
 * @see Url\HandleValidate
 * @see Url\HandleNormalize
 * @see Url\HandleCompare
 */
class Url
{
    use Url\HandleParse;
    use Url\HandleParts;
    use Url\HandleBuild;
    use Url\HandleQuery;
    use Url\HandleEncode;
    use Url\HandleValidate;
    use Url\HandleNormalize;
    use Url\HandleCompare;
}
