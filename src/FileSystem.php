<?php

namespace Tsc\CatStorageSystem;

use DateTime;
use Tsc\CatStorageSystem\Exceptions\RootDirectoryNotDefinedException;

class FileSystem implements FileSystemInterface
{
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
     * @throws RootDirectoryNotDefinedException{@
     */
    public function createDirectory(DirectoryInterface $directory, DirectoryInterface $parent)
    {
        if (is_null($this->root)) {
            throw new RootDirectoryNotDefinedException;
        }

        $directory->setPath($this->root->getPath() . '/' . $directory->getName());

        mkdir($directory->getPath(), 0777, true);

        $directory->setCreatedTime(new Datetime);

        return $directory;
    }

    public function deleteDirectory(DirectoryInterface $directory)
    {
        return true;
    }

    public function renameDirectory(DirectoryInterface $directory, $newName)
    {
        return $directory;
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
}
