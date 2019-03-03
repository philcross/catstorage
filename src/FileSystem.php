<?php

namespace Tsc\CatStorageSystem;

use DateTime;
use Tsc\CatStorageSystem\Exceptions;
use Tsc\CatStorageSystem\Traits\DirectoryHelpers;

class FileSystem implements FileSystemInterface
{
    use DirectoryHelpers;

    /** @var DirectoryInterface */
    private $root;

    public function __construct(DirectoryInterface $root = null)
    {
        $this->root = $root;
    }

    public function createFile(FileInterface $file, DirectoryInterface $parent)
    {
        if (!$this->pathIsWithinRoot($parent)) {
            throw new Exceptions\DirectoryMustBeWithinRootException;
        }

        $parentInfo = pathinfo($parent->getPath());

        if (!is_dir($parent->getPath())) {
            $parent = $this->createDirectory(
                $parent,
                (new Directory)->setName(basename($parentInfo['dirname']))->setPath($parentInfo['dirname'])
            );
        }

        $size = file_put_contents($parent->getPath() . '/' . $file->getName(), $file->getContent());

        $file->setSize($size)
            ->setCreatedTime(new DateTime)
            ->setModifiedTime(new DateTime)
            ->setParentDirectory($parent);

        return $file;
    }

    public function updateFile(FileInterface $file)
    {
        if (!$this->pathIsWithinRoot($file->getParentDirectory())) {
            throw new Exceptions\DirectoryMustBeWithinRootException;
        }

        $handle = fopen($file->getPath(), 'w');

        $size = fwrite($handle, $file->getContent());

        fclose($handle);

        $file->setSize($size);
        $file->setCreatedTime($file->getCreatedTime() ?: new DateTime);
        $file->setModifiedTime(new DateTime);

        return $file;
    }

    public function renameFile(FileInterface $file, $newName)
    {
        $path = pathinfo($file->getPath());
        $newPath = $path['dirname'] . '/' . $newName;

        if (file_exists($newPath)) {
            throw new Exceptions\FileAlreadyExistsException;
        }

        rename($file->getPath(), $newPath);

        $file->setName($newName);
        $file->setModifiedTime(new DateTime);

        return $file;
    }

    public function deleteFile(FileInterface $file)
    {
        @unlink($file->getPath());

        return true;
    }

    public function createRootDirectory(DirectoryInterface $directory)
    {
        $this->root = $directory;

        if (is_dir($this->root->getPath())) {
            $this->root->setCreatedTime(DateTime::createFromFormat('U', filectime($this->root->getPath())));
        } else {
            mkdir($directory->getPath(), 0777, true);

            $this->root->setCreatedTime(new DateTime);
        }

        return $this->root;
    }

    /**
     * @param DirectoryInterface $directory
     * @param DirectoryInterface $parent
     *
     * @return DirectoryInterface
     *
     * @throws Exceptions\RootDirectoryNotDefinedException{@
     */
    public function createDirectory(DirectoryInterface $directory, DirectoryInterface $parent)
    {
        if (is_null($this->root)) {
            throw new Exceptions\RootDirectoryNotDefinedException;
        }

        $directory->setPath($parent->getPath() . '/' . $directory->getName());

        mkdir($directory->getPath(), 0777, true);

        $directory->setCreatedTime(new Datetime);

        return $directory;
    }

    /**
     * @param DirectoryInterface $directory
     *
     * @return bool
     *
     * @throws Exceptions\DirectoryMustBeWithinRootException
     */
    public function deleteDirectory(DirectoryInterface $directory)
    {
        if (!is_dir($directory->getPath())) {
            return true;
        }

        if (!$this->pathIsWithinRoot($directory)) {
            throw new Exceptions\DirectoryMustBeWithinRootException;
        }

        return $this->recursiveDeleteDirectories($directory->getPath());
    }

    public function renameDirectory(DirectoryInterface $directory, $newName)
    {
        $base = pathinfo($directory->getPath(), PATHINFO_DIRNAME);
        $newPath = $base . '/' . trim($newName, '/');

        $moveTo = (new Directory)
            ->setName(basename($newPath))
            ->setPath($newPath);

        if (!$this->pathIsWithinRoot($moveTo)) {
            throw new Exceptions\DirectoryMustBeWithinRootException;
        }

        if (!is_dir($newPath)) {
            mkdir($newPath, 0777, true);
        }

        rename($directory->getPath(), $newPath);

        return $moveTo->setCreatedTime(new DateTime);
    }

    public function getDirectoryCount(DirectoryInterface $directory)
    {
        return count($this->getDirectories($directory));
    }

    public function getFileCount(DirectoryInterface $directory)
    {
        return count($this->getFiles($directory));
    }

    public function getDirectorySize(DirectoryInterface $directory)
    {
        if (!$this->pathIsWithinRoot($directory)) {
            throw new Exceptions\DirectoryMustBeWithinRootException;
        }

        $size = 0;

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory->getPath(), \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    public function getDirectories(DirectoryInterface $directory)
    {
        if (!$this->pathIsWithinRoot($directory)) {
            throw new Exceptions\DirectoryMustBeWithinRootException;
        }

        return array_map(function ($path) {
            return (new Directory)
                ->setName(basename($path))
                ->setCreatedTime(DateTime::createFromFormat('U', filectime($path)))
                ->setPath($path);
        }, array_filter(glob($directory->getPath() . '/*'), 'is_dir'));
    }

    public function getFiles(DirectoryInterface $directory)
    {
        if (!$this->pathIsWithinRoot($directory)) {
            throw new Exceptions\DirectoryMustBeWithinRootException;
        }

        $contents = new \DirectoryIterator($directory->getPath());

        $files = [];

        foreach ($contents as $item) {
            if ($item->isFile()) {
                $files[] = (new File)
                    ->setName($item->getBasename())
                    ->setCreatedTime(DateTime::createFromFormat('U', $item->getCTime()))
                    ->setModifiedTime(DateTime::createFromFormat('U', $item->getMTime()))
                    ->setSize($item->getSize())
                    ->setContent(file_get_contents($item->getPathname()))
                    ->setParentDirectory($directory);
            }
        }

        return $files;
    }

    private function pathIsWithinRoot(DirectoryInterface $directory)
    {
        $rootPath      = $this->realpath($this->root->getPath());
        $directoryPath = $this->realpath($directory->getPath());

        return $rootPath !== ''
            && substr($directoryPath, 0, strlen($rootPath)) === $rootPath;
    }
}
