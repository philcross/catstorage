<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Adapters\AdapterInterface;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\DirectoryInterface;
use Tsc\CatStorageSystem\FileSystem;

class RenameDirectoryTest extends DirectoryTestCase
{
    public function test_an_existing_directory_can_be_renamed()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('renameDirectory')->once()->with('/images', 'img')->andReturn([
            'name'     => 'img',
            'pathname' => '/img',
            'basename' => '/',
            'created'  => date('Y-m-d H:i:s'),
        ]);

        $filesystem = new FileSystem($adapter);

        $result = $filesystem->renameDirectory(Directory::hydrate('/images'), 'img');

        $this->assertInstanceOf(DirectoryInterface::class, $result);

        $this->assertDirectoryEquals($result, '/img');
    }
}
