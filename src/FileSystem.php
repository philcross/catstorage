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
        return $file;
    }

    public function updateFile(FileInterface $file)
    {
        return $file;
    }

    public function renameFile(FileInterface $file, $newName)
    {
        return $file;
    }

    public function deleteFile(FileInterface $file)
    {
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

        $directory->setPath($this->root->getPath() . '/' . $directory->getName());

        mkdir($directory->getPath(), 0777, true);

        $directory->setCreatedTime(new Datetime);

        return $directory;
    }

    /**
     * @param DirectoryInterface $directory
     *
     * @return bool
     *
     * @throws Exceptions\CannotDeleteDirectoryOutsideRootException
     */
    public function deleteDirectory(DirectoryInterface $directory)
    {
        if (!is_dir($directory->getPath())) {
            return true;
        }

        if (!$this->pathIsWithinRoot($directory)) {
            throw new Exceptions\CannotDeleteDirectoryOutsideRootException;
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
            throw new Exceptions\CannotMoveDirectoryOutsideRootException;
        }

        if (!is_dir($newPath)) {
            mkdir($newPath, 0777, true);
        }

        rename($directory->getPath(), $newPath);

        return $moveTo->setCreatedTime(new DateTime);
    }

    public function getDirectoryCount(DirectoryInterface $directory)
    {
        return 0;
    }

    public function getFileCount(DirectoryInterface $directory)
    {
        return 0;
    }

    public function getDirectorySize(DirectoryInterface $directory)
    {
        return 0;
    }

    public function getDirectories(DirectoryInterface $directory)
    {
        return [];
    }

    public function getFiles(DirectoryInterface $directory)
    {
        return [];
    }

    private function pathIsWithinRoot(DirectoryInterface $directory)
    {
        $rootPath      = $this->realpath($this->root->getPath());
        $directoryPath = $this->realpath($directory->getPath());

        return $rootPath !== ''
            && substr($directoryPath, 0, strlen($rootPath)) === $rootPath;
    }
}
