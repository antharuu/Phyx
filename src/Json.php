<?php

declare(strict_types=1);

namespace Phyx;

/**
 * Provide a predictable interface for JSON operations.
 *
 * This façade aggregates encoding, decoding, validation, and error handling
 * functionality. All operations default to throwing {@see \JsonException}
 * on failure unless 'try' variants are used.
 *
 * @see \Phyx\Json\HandleEncode
 * @see \Phyx\Json\HandleDecode
 * @see \Phyx\Json\HandleValidate
 * @see \Phyx\Json\HandleError
 * @see \Phyx\Json\HandleAccess
 */
class Json
{
    use Json\HandleEncode;
    use Json\HandleDecode;
    use Json\HandleValidate;
    use Json\HandleError;
    use Json\HandleAccess;
}
