<?php declare(strict_types=1);

namespace StudyPortals\Utils;

/**
 * @codeCoverageIgnore
 */
abstract class File
{

    /**
     * Get the extension of a file.
     *
     * <p>Returns an empty string in case the file has no extension (or if no
     * file name was provided). The extension returned is always lowercase.</p>
     *
     * @param string $file
     * @return string
     */

    public static function getExtension($file): ?string
    {

        $file = trim($file);
        $extension = strtolower(substr((string) strrchr($file, '.'), 1));

        if ($extension === $file) {
            return '';
        }

        return $extension;
    }

    /**
     * Trim a path and ensure it has a (single) trailing-slash.
     *
     * @param string $path
     * @return string
     */

    public static function trimPath($path): string
    {

        return rtrim(trim($path), '/\\') . '/';
    }
}
