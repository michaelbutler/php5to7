<?php

namespace michaelbutler\php5to7;

class FileProvider
{
    /**
     * @param string $path Input file path
     * @return \Traversable
     */
    public static function fromSingle($path)
    {
        return new \ArrayIterator([
            new \SplFileInfo($path),
        ]);
    }

    /**
     * @param string $path The input directory
     * @return \Traversable
     */
    public static function fromDir($path)
    {
        // Construct the iterator
        $it = new \RecursiveDirectoryIterator(
            rtrim($path, '/'),
            \FilesystemIterator::SKIP_DOTS
        );
        return new \RecursiveIteratorIterator($it);
    }
}
