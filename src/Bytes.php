<?php

declare(strict_types=1);

namespace Phyx;

/**
 * Static facade for manipulating PHP strings as raw byte sequences.
 *
 * Provides a comprehensive set of tools for handling binary data, including
 * encoding, decoding, slicing, and random generation. Offsets and lengths
 * are always expressed in bytes. This class intentionally avoids multibyte-aware
 * functions as it operates on raw binary buffers rather than text.
 *
 * @see \Phyx\Str for text-based manipulation.
 */
class Bytes
{
    use Bytes\HandleBase64;
    use Bytes\HandleByte;
    use Bytes\HandleHex;
    use Bytes\HandleLength;
    use Bytes\HandlePack;
    use Bytes\HandleRandom;
    use Bytes\HandleSlice;
    use Bytes\HandleSplit;
}
