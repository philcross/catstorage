<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileInterface;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;

class CreateFileTest extends FileTestCase
{
    public function test_it_can_create_a_new_file()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('createFile')->once()->with('/images/cat_1.txt', 'here be catz')->andReturn([
            'name'     => 'cat_1.txt',
            'pathname' => '/images/cat_1.txt',
            'basename' => '/images',
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => 12,
        ]);

        $this->filesystem->setAdapter($adapter);

        $create = (new File)->setName('cat_1.txt')->setContent('here be catz');

        $image = $this->filesystem->createFile($create, Directory::hydrate('/images'));

        $this->assertInstanceOf(FileInterface::class, $image);
        $this->assertEquals('cat_1.txt', $image->getName());
        $this->assertEquals('/images/cat_1.txt', $image->getPath());
        $this->assertEquals(date('Y-m-d H:i:s'), $image->getCreatedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(date('Y-m-d H:i:s'), $image->getModifiedTime()->format('Y-m-d H:i:s'));
    }
}
