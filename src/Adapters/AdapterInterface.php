<?php

namespace Tsc\CatStorageSystem\Adapters;

interface AdapterInterface
{
    /**
     * @param $path
     *
     * @return array
     *
     * @throws \Exception
     */
    public function createDirectory($path);

    /**
     * @param string $path
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function deleteDirectory($path);

    /**
     * @param string $oldPath
     * @param string $newPath
     *
     * @return array
     *
     * @throws \Exception
     */
    public function renameDirectory($oldPath, $newPath);

    /**
     * @param string $path
     *
     * @return array
     *
     * @throws \Exception
     */
    public function listDirectories($path);

    /**
     * @param string $path
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getDirectory($path);

    /**
     * @param $path
     *
     * @return int
     * @throws \Exception
     */
    public function getDirectorySize($path);

    /**
     * @param string $path
     * @param string $content
     *
     * @return array
     *
     * @throws \Exception
     */
    public function createFile($path, $content);

    /**
     * @param string $path
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function deleteFile($path);

    /**
     * @param string $path
     * @param string $newName
     *
     * @return array
     *
     * @throws \Exception
     */
    public function renameFile($path, $newName);

    /**
     * @param string $path
     * @param string $content
     *
     * @return array
     *
     * @throws \Exception
     */
    public function updateFile($path, $content);

    /**
     * @param string $path
     *
     * @return array
     *
     * @throws \Exception
     */
    public function listFiles($path);

    /**
     * @param string $path
     *
     * @return bool|string
     *
     * @throws \Exception
     */
    public function readFile($path);

    /**
     * @param string $path
     *
     * @return array
     *
     * @throws \Exception
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
