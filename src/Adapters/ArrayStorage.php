<?php

namespace Tsc\CatStorageSystem\Adapters;

use DateTime;
use DirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Tsc\CatStorageSystem\Exceptions\PathNotInRootException;
use Tsc\CatStorageSystem\Exceptions\FileDoesntExistException;
use Tsc\CatStorageSystem\Exceptions\DirectoryDoesntExistException;
use Tsc\CatStorageSystem\Exceptions\DirectoryAlreadyExistException;

class ArrayStorage implements AdapterInterface
{
    /** @var string */
    private $root;

    /** @var array */
    private $directories;

    /** @var array */
    private $files;

    /**
     * Constructor
     *
     * @param string $root
     * @param array $directories
     * @param array $files
     */
    public function __construct($root, array $directories, array $files)
    {
        $this->root        = $this->normalizePath($root);
        $this->directories = $directories;
        $this->files       = $files;
    }

    /**
     * {@inheritdoc}
     */
    public function createDirectory($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);

        $pathinfo = pathinfo($normalizedPath);

        if (!$this->directoryExists($normalizedPath)) {
            $this->directories[$normalizedPath] = [
                'name'     => basename($normalizedPath),
                'pathname' => $normalizedPath,
                'basename' => $pathinfo['dirname'],
                'created'  => date('Y-m-d H:i:s'),
            ];
        }

        return $this->directories[$normalizedPath];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDirectory($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);

        if (!$this->directoryExists($normalizedPath)) {
            return true;
        }

        foreach ($this->directories as $directoryPath => $directory) {
            if ($this->startsWith($directoryPath, $normalizedPath)) {
                unset($this->directories[$directoryPath]);
            }
        }

        foreach ($this->files as $filePath => $file) {
            if ($this->startsWith($filePath, $normalizedPath)) {
                unset($this->files[$filePath]);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function renameDirectory($oldPath, $newPath)
    {
        $oldNormalizedPath = $this->normalizePath($oldPath);
        $newNormalizedPath = $this->normalizePath($newPath);

        $this->verifyPathIsWithinRoot($oldNormalizedPath);
        $this->verifyPathIsWithinRoot($newNormalizedPath);
        $this->verifyDirectoryExists($oldNormalizedPath);

        if ($this->directoryExists($newNormalizedPath)) {
            throw new DirectoryAlreadyExistException($newNormalizedPath);
        }

        foreach ($this->directories as $directoryPath => $directory) {
            if ($this->startsWith($directoryPath, $oldNormalizedPath)) {
                $newPath = str_replace($oldNormalizedPath, $newNormalizedPath, $directoryPath);

                $pathinfo = pathinfo($newPath);

                $directory['basename'] = $pathinfo['dirname'];
                $directory['pathname'] = $newPath;
                $directory['name'] = basename($newPath);

                $this->directories[$newPath] = $directory;
                unset($this->directories[$oldNormalizedPath]);
            }
        }

        foreach ($this->files as $filePath => $file) {
            if ($this->startsWith($filePath, $oldNormalizedPath)) {
                $newPath = str_replace($oldNormalizedPath, $newNormalizedPath, $filePath);

                $this->files[$newPath] = $file;
                unset($this->files[$oldNormalizedPath]);
            }
        }

        return $this->directories[$newNormalizedPath];
    }

    /**
     * {@inheritdoc}
     */
    public function listDirectories($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);
        $this->verifyDirectoryExists($normalizedPath);

        return array_values(array_filter($this->directories, function ($directory) use ($normalizedPath) {
            return $directory['basename'] === $normalizedPath;
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectory($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);
        $this->verifyDirectoryExists($normalizedPath);

        return $this->directories[$normalizedPath];
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectorySize($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);
        $this->verifyDirectoryExists($normalizedPath);

        $size = 0;

        foreach ($this->files as $file) {
            if ($this->startsWith($file['basename'], $normalizedPath)) {
                $size += $file['size'];
            }
        }

        return $size;
    }

    /**
     * {@inheritdoc}
     */
    public function createFile($path, $content)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);

        $pathinfo = pathinfo($normalizedPath);

        $this->files[$normalizedPath] = [
            'name'     => basename($normalizedPath),
            'pathname' => $normalizedPath,
            'basename' => $pathinfo['dirname'],
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'content'  => $content,
            'size'     => strlen($content),
        ];

        $file = $this->files[$normalizedPath];
        unset($file['content']);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);

        unset($this->files[$normalizedPath]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function renameFile($path, $newName)
    {
        $normalizedPath = $this->normalizePath($path);
        $normalizedName = $this->normalizePath($newName);

        $this->verifyPathIsWithinRoot($normalizedPath);
        $this->verifyPathIsWithinRoot($normalizedName);

        $pathinfo = pathinfo($normalizedName);

        if (!$this->directoryExists($pathinfo['dirname'])) {
            $this->createDirectory($pathinfo['dirname']);
        }

        $this->files[$normalizedName] = $this->files[$normalizedPath];
        $this->files[$normalizedName]['name'] = basename($normalizedName);
        $this->files[$normalizedName]['basename'] = $pathinfo['dirname'];
        $this->files[$normalizedName]['pathname'] = $normalizedName;

        unset($this->files[$normalizedPath]);

        $file = $this->files[$normalizedName];
        unset($file['content']);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile($path, $content)
    {
        return $this->createFile($path, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function listFiles($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);
        $this->verifyDirectoryExists($normalizedPath);

        $files = array_values(array_filter($this->files, function ($file) use ($normalizedPath) {
            return $file['basename'] === $normalizedPath;
        }));

        return array_map(function ($file) {
            unset($file['content']);

            return $file;
        }, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function readFile($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);
        $this->verifyFileExists($normalizedPath);

        return $this->files[$normalizedPath]['content'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFile($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);
        $this->verifyFileExists($normalizedPath);

        $file = $this->files[$normalizedPath];

        unset($file['content']);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function directoryExists($path)
    {
        return isset($this->directories[$path]);
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists($path)
    {
        return isset($this->files[$path]);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function normalizePath($path)
    {
        if ($this->pathIsWithinRoot($path)) {
            return DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);
        }

        return DIRECTORY_SEPARATOR . $this->realpath($this->root . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR));
    }

    /**
     * @param string $path
     *
     * @throws PathNotInRootException
     */
    private function verifyPathIsWithinRoot($path)
    {
        if (!$this->pathIsWithinRoot($path)) {
            throw new PathNotInRootException($path, $this->root);
        }
    }

    /**
     * @param string $path
     *
     * @throws DirectoryDoesntExistException
     */
    public function verifyDirectoryExists($path)
    {
        if (!$this->directoryExists($path)) {
            throw new DirectoryDoesntExistException($path);
        }
    }

    /**
     * @param string $path
     *
     * @throws FileDoesntExistException
     */
    public function verifyFileExists($path)
    {
        if (!$this->fileExists($path)) {
            throw new FileDoesntExistException($path);
        }
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function pathIsWithinRoot($path)
    {
        return $this->root !== '' && substr($path, 0, strlen($this->root)) === $this->root;
    }

    /**
     * @param string $path
     *
     * @return string
     *
     * @see http://php.net/manual/en/function.realpath.php#84012
     */
    protected function realpath($path)
    {
        $path      = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts     = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
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

    private function startsWith($haystack, $needle)
    {
        return stripos($haystack, $needle) === 0 && $needle !== '';
    }
}
