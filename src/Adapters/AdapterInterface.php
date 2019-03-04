<?php

namespace Tsc\CatStorageSystem\Adapters;

use Tsc\CatStorageSystem\Exceptions\PathNotInRootException;
use Tsc\CatStorageSystem\Exceptions\FileDoesntExistException;
use Tsc\CatStorageSystem\Exceptions\DirectoryDoesntExistException;
use Tsc\CatStorageSystem\Exceptions\DirectoryAlreadyExistException;

interface AdapterInterface
{
    /**
     * @param $path
     *
     * @return array
     *
     * @throws PathNotInRootException
     */
    public function createDirectory($path);

    /**
     * @param string $path
     *
     * @return bool
     *
     * @throws PathNotInRootException
     */
    public function deleteDirectory($path);

    /**
     * @param string $oldPath
     * @param string $newPath
     *
     * @return array
     *
     * @throws PathNotInRootException
     * @throws DirectoryDoesntExistException
     * @throws DirectoryAlreadyExistException
     */
    public function renameDirectory($oldPath, $newPath);

    /**
     * @param string $path
     *
     * @return array
     *
     * @throws PathNotInRootException
     * @throws DirectoryDoesntExistException
     */
    public function listDirectories($path);

    /**
     * @param string $path
     *
     * @return array
     *
     * @throws PathNotInRootException
     * @throws DirectoryDoesntExistException
     */
    public function getDirectory($path);

    /**
     * @param $path
     *
     * @return int
     *
     * @throws PathNotInRootException
     * @throws DirectoryDoesntExistException
     */
    public function getDirectorySize($path);

    /**
     * @param string $path
     * @param string $content
     *
     * @return array
     *
     * @throws PathNotInRootException
     */
    public function createFile($path, $content);

    /**
     * @param string $path
     *
     * @return bool
     *
     * @throws PathNotInRootException
     */
    public function deleteFile($path);

    /**
     * @param string $path
     * @param string $newName
     *
     * @return array
     *
     * @throws PathNotInRootException
     */
    public function renameFile($path, $newName);

    /**
     * @param string $path
     * @param string $content
     *
     * @return array
     *
     * @throws PathNotInRootException
     */
    public function updateFile($path, $content);

    /**
     * @param string $path
     *
     * @return array
     *
     * @throws PathNotInRootException
     * @throws DirectoryDoesntExistException
     */
    public function listFiles($path);

    /**
     * @param string $path
     *
     * @return bool|string
     *
     * @throws PathNotInRootException
     * @throws FileDoesntExistException
     */
    public function readFile($path);

    /**
     * @param string $path
     *
     * @return array
     *
     * @throws PathNotInRootException
     * @throws FileDoesntExistException
     */
    public function getFile($path);

    /**
     * @param string $path
     *
     * @return bool
     */
    public function directoryExists($path);

    /**
     * @param string $path
     *
     * @return bool
     */
    public function fileExists($path);
}
