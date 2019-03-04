<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Adapters\AdapterInterface;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\DirectoryInterface;

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

        $this->filesystem->setAdapter($adapter);

        $result = $this->filesystem->renameDirectory(Directory::hydrate('/images'), 'img');

        $this->assertInstanceOf(DirectoryInterface::class, $result);

        $this->assertDirectoryEquals($result, '/img');
    }
}
