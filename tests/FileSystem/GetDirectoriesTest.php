<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;
use Tsc\CatStorageSystem\FileSystem;

class GetDirectoriesTest extends DirectoryTestCase
{
    public function test_it_can_return_an_array_of_sub_directories()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('listDirectories')->once()->with('/images')->andReturn([
            [
                'name'     => 'cats',
                'pathname' => '/images/cats',
                'basename' => '/images',
                'created'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'     => 'lions',
                'pathname' => '/images/lions',
                'basename' => '/images',
                'created'  => date('Y-m-d H:i:s'),
            ]
        ]);

        $filesystem = new FileSystem($adapter);

        $result = $filesystem->getDirectories(Directory::hydrate('/images'));

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->assertDirectoryEquals($result[0], '/images/cats');
        $this->assertDirectoryEquals($result[1], '/images/lions');
    }

    public function test_it_can_return_the_number_of_directories_in_a_given_directory()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('listDirectories')->once()->with('/images')->andReturn([[], []]);

        $filesystem = new FileSystem($adapter);

        $images = Directory::hydrate('/images');

        $this->assertEquals(2, $filesystem->getDirectoryCount($images));
    }

    public function test_it_can_return_the_size_of_the_directory()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('getDirectorySize')->once()->with('/images')->andReturn(10);

        $filesystem = new FileSystem($adapter);

        $this->assertEquals(10, $filesystem->getDirectorySize(Directory::hydrate('/images')));
    }
}
