<?php

namespace Tsc\CatStorageSystem\Traits;

trait DirectoryHelpers
{
    /**
     * @param string $path
     *
     * @return string
     *
     * @see http://php.net/manual/en/function.realpath.php#84012
     */
    protected function realpath($path)
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = [];

        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            } elseif ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function recursiveDeleteDirectories($path)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return rmdir($path);
    }
}
