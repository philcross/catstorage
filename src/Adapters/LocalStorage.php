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

class LocalStorage implements AdapterInterface
{
    /** @var string */
    private $root;

    /**
     * Constructor
     *
     * @param string $root
     */
    public function __construct($root)
    {
        $this->root = $this->normalizePath($root);
    }

    /**
     * {@inheritdoc}
     */
    public function createDirectory($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);

        if (!$this->directoryExists($normalizedPath)) {
            mkdir($normalizedPath, 0777, true);
        }

        $pathinfo = pathinfo($normalizedPath);

        return [
            'name'     => basename($normalizedPath),
            'pathname' => $normalizedPath,
            'basename' => $pathinfo['dirname'],
            'created'  => \DateTime::createFromFormat('U', filectime($normalizedPath))->format('Y-m-d H:i:s'),
        ];
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

        $contents = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($normalizedPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($contents as $item) {
            $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
        }

        return rmdir($normalizedPath);
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

        rename($oldNormalizedPath, $newNormalizedPath);

        $pathinfo = pathinfo($newNormalizedPath);

        return [
            'name'     => basename($newNormalizedPath),
            'pathname' => $newNormalizedPath,
            'basename' => $pathinfo['dirname'],
            'created'  => \DateTime::createFromFormat('U', filectime($newNormalizedPath))->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function listDirectories($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);
        $this->verifyDirectoryExists($normalizedPath);

        $contents = new DirectoryIterator($normalizedPath);
        $directories = [];

        foreach ($contents as $item) {
            if (!$item->isDir() || $item->isDot()) {
                continue;
            }

            $pathinfo = pathinfo($item->getPathname());

            $directories[] = [
                'name'     => basename($item->getPathname()),
                'pathname' => $item->getPathname(),
                'basename' => $pathinfo['dirname'],
                'created'  => \DateTime::createFromFormat('U', $item->getCTime())->format('Y-m-d H:i:s'),
            ];
        }

        return $directories;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectory($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);
        $this->verifyDirectoryExists($normalizedPath);

        $pathinfo = pathinfo($normalizedPath);

        return [
            'name'     => basename($normalizedPath),
            'pathname' => $normalizedPath,
            'basename' => $pathinfo['dirname'],
            'created'  => DateTime::createFromFormat('U', filectime($normalizedPath))->format('Y-m-d H:i:s'),
        ];
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

        $content = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($normalizedPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($content as $item) {
            $size += $item->getSize();
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

        $handle = fopen($normalizedPath, 'w+');

        $size = fwrite($handle, $content);

        fclose($handle);

        $pathinfo = pathinfo($normalizedPath);

        return [
            'name'     => basename($normalizedPath),
            'pathname' => $normalizedPath,
            'basename' => $pathinfo['dirname'],
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => $size,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);

        if (!$this->fileExists($normalizedPath)) {
            return true;
        }

        return unlink($normalizedPath);
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

        rename($normalizedPath, $normalizedName);

        return [
            'name'     => basename($normalizedName),
            'pathname' => $normalizedName,
            'basename' => $pathinfo['dirname'],
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => filesize($normalizedName),
        ];
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

        $contents = new DirectoryIterator($normalizedPath);
        $files    = [];

        foreach ($contents as $item) {
            if (!$item->isFile()) {
                continue;
            }

            $pathinfo = pathinfo($item->getPathname());

            $files[] = [
                'name'     => $item->getBasename(),
                'pathname' => $item->getPathname(),
                'basename' => $pathinfo['dirname'],
                'created'  => DateTime::createFromFormat('U', $item->getCTime())->format('Y-m-d H:i:s'),
                'modified' => DateTime::createFromFormat('U', $item->getMTime())->format('Y-m-d H:i:s'),
                'size'     => $item->getSize(),
            ];
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function readFile($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);
        $this->verifyFileExists($normalizedPath);

        $handle = fopen($normalizedPath, 'r');

        $content = fread($handle, filesize($normalizedPath));

        fclose($handle);

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile($path)
    {
        $normalizedPath = $this->normalizePath($path);

        $this->verifyPathIsWithinRoot($normalizedPath);
        $this->verifyFileExists($normalizedPath);

        $pathinfo = pathinfo($normalizedPath);

        return [
            'name'     => basename($normalizedPath),
            'pathname' => $normalizedPath,
            'basename' => $pathinfo['dirname'],
            'created'  => DateTime::createFromFormat('U', filectime($normalizedPath))->format('Y-m-d H:i:s'),
            'modified' => DateTime::createFromFormat('U', filemtime($normalizedPath))->format('Y-m-d H:i:s'),
            'size'     => filesize($normalizedPath),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function directoryExists($path)
    {
        return is_dir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists($path)
    {
        return file_exists($path) && is_file($path);
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
}
