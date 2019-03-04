<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem\Adapters;

use Tsc\CatStorageSystem\Adapters\LocalStorage;
use Tsc\CatStorageSystem\Traits\DirectoryHelpers;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;

class LocalStorageAdapterTest extends AbstractAdapterTest
{
    use DirectoryHelpers;

    protected function getRootPath()
    {
        return __DIR__.'/local_storage';
    }

    protected function getAdapter(): AdapterInterface
    {
        return new LocalStorage($this->getRootPath());
    }

    protected function setUp()
    {
        parent::setUp();

        mkdir($this->getRootPath(), 0777, true);
        mkdir($this->getRootPath() . '/files', 0777, true);

        file_put_contents($this->getRootPath(). '/files/test.txt', 'test file');
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->recursiveDeleteDirectories($this->getRootPath());
    }
}
