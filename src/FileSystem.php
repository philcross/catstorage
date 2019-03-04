<?php

namespace Tsc\CatStorageSystem;

use DateTime;
use Tsc\CatStorageSystem\Models\File;
use Tsc\CatStorageSystem\Models\Directory;
use Tsc\CatStorageSystem\Models\FileInterface;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;
use Tsc\CatStorageSystem\Models\DirectoryInterface;

class FileSystem implements FileSystemInterface
{
    /** @var AdapterInterface */
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\PathNotInRootException
     */
    public function createFile(FileInterface $file, DirectoryInterface $parent)
    {
        $newFile = $this->adapter->createFile($parent->getPath() . '/' . $file->getName(), $file->getContent());

        return (new File)
            ->setName($newFile['name'])
            ->setParentDirectory(Directory::hydrate($newFile['basename']))
            ->setCreatedTime(new DateTime($newFile['created']))
            ->setModifiedTime(new DateTime($newFile['modified']))
            ->setSize($newFile['size']);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\DirectoryDoesntExistException
     * @throws Exceptions\PathNotInRootException
     */
    public function updateFile(FileInterface $file)
    {
        $updatedFile = $this->adapter->updateFile($file->getPath(), $file->getContent());
        $parent      = $this->adapter->getDirectory($updatedFile['basename']);

        $parentDirectory = (new Directory)
            ->setName($parent['name'])
            ->setPath($parent['pathname'])
            ->setCreatedTime(new DateTime($parent['created']));

        return (new File)
            ->setName($updatedFile['name'])
            ->setParentDirectory($parentDirectory)
            ->setSize($updatedFile['size'])
            ->setCreatedTime(new DateTime($updatedFile['created']))
            ->setModifiedTime(new DateTime($updatedFile['modified']));
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\DirectoryDoesntExistException
     * @throws Exceptions\PathNotInRootException
     */
    public function renameFile(FileInterface $file, $newName)
    {
        $renamedFile = $this->adapter->renameFile($file->getPath(), $newName);
        $parent      = $this->adapter->getDirectory($renamedFile['basename']);

        $parentDirectory = (new Directory)
            ->setName($parent['name'])
            ->setPath($parent['pathname'])
            ->setCreatedTime(new DateTime($parent['created']));

        return (new File)
            ->setName($renamedFile['name'])
            ->setParentDirectory($parentDirectory)
            ->setSize($renamedFile['size'])
            ->setCreatedTime(new DateTime($renamedFile['created']))
            ->setModifiedTime(new Datetime($renamedFile['modified']));
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\PathNotInRootException
     */
    public function deleteFile(FileInterface $file)
    {
        return $this->adapter->deleteFile($file->getPath());
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function createRootDirectory(DirectoryInterface $directory)
    {
        //
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\PathNotInRootException
     */
    public function createDirectory(DirectoryInterface $directory, DirectoryInterface $parent)
    {
        $directory = $this->adapter->createDirectory($parent->getPath() . '/' . $directory->getName());

        return (new Directory)
            ->setName($directory['name'])
            ->setPath($directory['pathname'])
            ->setCreatedTime(new DateTime($directory['created']));
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\PathNotInRootException
     */
    public function deleteDirectory(DirectoryInterface $directory)
    {
        return $this->adapter->deleteDirectory($directory->getPath());
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\DirectoryAlreadyExistException
     * @throws Exceptions\DirectoryDoesntExistException
     * @throws Exceptions\PathNotInRootException
     */
    public function renameDirectory(DirectoryInterface $directory, $newName)
    {
        $renamedDirectory = $this->adapter->renameDirectory($directory->getPath(), $newName);

        return (new Directory)
            ->setName($renamedDirectory['name'])
            ->setPath($renamedDirectory['pathname'])
            ->setCreatedTime(new DateTime($renamedDirectory['created']));
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\DirectoryDoesntExistException
     * @throws Exceptions\PathNotInRootException
     */
    public function getDirectoryCount(DirectoryInterface $directory)
    {
        return count($this->adapter->listDirectories($directory->getPath()));
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\DirectoryDoesntExistException
     * @throws Exceptions\PathNotInRootException
     */
    public function getFileCount(DirectoryInterface $directory)
    {
        return count($this->adapter->listFiles($directory->getPath()));
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\DirectoryDoesntExistException
     * @throws Exceptions\PathNotInRootException
     */
    public function getDirectorySize(DirectoryInterface $directory)
    {
        return $this->adapter->getDirectorySize($directory->getPath());
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\DirectoryDoesntExistException
     * @throws Exceptions\PathNotInRootException
     */
    public function getDirectories(DirectoryInterface $directory)
    {
        return array_map(function ($directory) {
            return (new Directory)
                ->setName($directory['name'])
                ->setPath($directory['pathname'])
                ->setCreatedTime(new DateTime($directory['created']));
        }, $this->adapter->listDirectories($directory->getPath()));
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exceptions\DirectoryDoesntExistException
     * @throws Exceptions\PathNotInRootException
     */
    public function getFiles(DirectoryInterface $directory)
    {
        return array_map(function ($file) {
            return (new File)
                ->setName($file['name'])
                ->setParentDirectory(Directory::hydrate($file['basename']))
                ->setSize($file['size'])
                ->setCreatedTime(new DateTime($file['created']))
                ->setModifiedTime(new DateTime($file['modified']));
        }, $this->adapter->listFiles($directory->getPath()));
    }
}
