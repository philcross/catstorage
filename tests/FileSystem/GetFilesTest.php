<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Adapters\AdapterInterface;
use Tsc\CatStorageSystem\Adapters\LocalStorage;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Exceptions\DirectoryMustBeWithinRootException;
use Tsc\CatStorageSystem\FileInterface;
use Tsc\CatStorageSystem\FileSystem;

class GetFilesTest extends FileTestCase
{
    public function test_it_can_return_an_array_of_files_within_a_directory()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('listFiles')->once()->with('/images')->andReturn([
            [
                'name'     => 'grumpy_cat.gif',
                'pathname' => '/images/grumpy_cat.gif',
                'basename' => '/images',
                'created'  => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
                'size'     => 10,
            ],
            [
                'name'     => 'fat_cat.gif',
                'pathname' => '/images/fat_cat.gif',
                'basename' => '/images',
                'created'  => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
                'size'     => 20,
            ]
        ]);

        $filesystem = new FileSystem($adapter);

        /** @var FileInterface[] $files */
        $files = $filesystem->getFiles(Directory::hydrate('/images'));

        $this->assertInternalType('array', $files);
        $this->assertCount(2, $files);

        $this->assertEquals('/images/grumpy_cat.gif', $files[0]->getPath());
        $this->assertEquals('/images/fat_cat.gif', $files[1]->getPath());
    }

    public function test_it_can_return_the_number_of_files_in_a_directory()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('listFiles')->once()->with('/images')->andReturn([[], []]);

        $filesystem = new FileSystem($adapter);

        $this->assertEquals(2, $filesystem->getFileCount(Directory::hydrate('/images')));
    }
}
