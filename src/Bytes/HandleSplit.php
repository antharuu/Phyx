<?php

declare(strict_types=1);

namespace Phyx\Bytes;

/**
 * Byte splitting helpers for {@see \Phyx\Bytes}.
 */
trait HandleSplit
{
    /**
     * Split a byte string into an array of fixed-size chunks.
     *
     * Breaks the string into smaller segments of the specified length. The
     * final chunk may be shorter than the requested length.
     *
     * @param  string  $bytes   The raw byte string to split.
     * @param  int     $length  The size of each chunk in bytes. Defaults to 1.
     * @return list<string>  A list of byte chunks.
     *
     * @throws \ValueError When length is less than 1.
     *
     * @example Bytes::split('Phyx', 2) // => ['Ph', 'yx']
     * @example Bytes::split('ABC', 1) // => ['A', 'B', 'C']
     *
     * @see str_split
     */
    public static function split(string $bytes, int $length = 1): array
    {
        if ($length < 1) {
            throw new \ValueError('Split length must be greater than 0.');
        }

        return str_split($bytes, $length);
    }

    /**
     * Chunk a byte string into a formatted string with separators.
     *
     * Inserts a separator after every fixed number of bytes. Commonly used for
     * formatting base64 output for emails.
     *
     * @param  string  $bytes      The raw byte string to chunk.
     * @param  int     $length     The number of bytes between separators. Defaults to 76.
     * @param  string  $separator  The separator string to insert. Defaults to "\r\n".
     * @return string  The formatted byte string.
     *
     * @throws \ValueError When length is less than 1.
     *
     * @example Bytes::chunk('PhyxPhyx', 4, '-') // => "Phyx-Phyx-"
     *
     * @see chunk_split
     */
    public static function chunk(string $bytes, int $length = 76, string $separator = "\r\n"): string
    {
        if ($length < 1) {
            throw new \ValueError('Chunk length must be greater than 0.');
        }

        if ($bytes === '') {
            return '';
        }

        return chunk_split($bytes, $length, $separator);
    }
}
