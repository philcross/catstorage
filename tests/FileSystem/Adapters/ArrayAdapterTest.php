<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem\Adapters;

use Tsc\CatStorageSystem\Adapters\ArrayStorage;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;

class ArrayAdapterTest extends AbstractAdapterTest
{
    protected function getRootPath()
    {
        return '/local_storage';
    }

    protected function getAdapter(): AdapterInterface
    {
        return new ArrayStorage($this->getRootPath(), [
            '/local_storage' => [
                'name'     => 'local_storage',
                'pathname' => $this->getRootPath(),
                'basename' => '/',
                'created'  => date('Y-m-d H:i:s'),
            ],
            '/local_storage/files' => [
                'name'     => 'files',
                'pathname' => $this->getRootPath().'/files',
                'basename' => $this->getRootPath(),
                'created'  => date('Y-m-d H:i:s'),
            ],
        ], [
            '/local_storage/files/test.txt' => [
                'name'     => 'test.txt',
                'pathname' => $this->getRootPath().'/files/test.txt',
                'basename' => $this->getRootPath().'/files',
                'created'  => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
                'content'  => 'test file',
                'size'     => 9,
            ],
        ]);
    }
}
