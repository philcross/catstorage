<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileSystem;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;

class CreateDirectoryTest extends DirectoryTestCase
{
    /**
     * @deprecated
     */
    public function it_can_create_a_root_directory()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);

        $filesystem = new FileSystem($adapter);

        $root = (new Directory)->setName('files')->setPath($this->root.'/files');

        $directory = $filesystem->createRootDirectory($root);

        $this->assertRecentlyCreatedDirectory($directory, $this->root.'/files');
    }

    public function test_it_can_create_a_new_directory()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('createDirectory')->once()->with('/storage/files')->andReturn([
            'name'     => 'files',
            'pathname' => '/storage/files',
            'basename' => '/storage',
            'created'  => date('Y-m-d H:i:s'),
        ]);

        $filesystem = new FileSystem($adapter);

        $directory = $filesystem->createDirectory(
            (new Directory)->setName('files'),
            Directory::hydrate('/storage')
        );

        $this->assertEquals('files', $directory->getName());
        $this->assertEquals('/storage/files', $directory->getPath());
        $this->assertEquals(date('Y-m-d H:i:s'), $directory->getCreatedTime()->format('Y-m-d H:i:s'));
    }
}
