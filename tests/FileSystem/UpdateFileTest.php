<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Adapters\AdapterInterface;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\FileInterface;
use Tsc\CatStorageSystem\FileSystem;

class UpdateFileTest extends FileTestCase
{
    public function test_it_can_update_the_contents_of_a_file()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('updateFile')->once()->with('/images/test.txt', 'updated test')->andReturn([
            'name'     => 'test.txt',
            'pathname' => '/images/test.txt',
            'basename' => '/images',
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => 10,
        ]);

        $adapter->shouldReceive('getDirectory')->once()->with('/images')->andReturn([
            'name'     => 'images',
            'pathname' => '/images',
            'basename' => '/',
            'created'  => date('Y-m-d H:i:s'),
        ]);

        $filesystem = new FileSystem($adapter);

        $file = (new File)->setName('test.txt')->setParentDirectory(Directory::hydrate('/images'));

        $file->setContent('updated test');

        $result = $filesystem->updateFile($file);

        $this->assertInstanceOf(FileInterface::class, $result);
        $this->assertEquals(date('Y-m-d H:i:s'), $result->getModifiedTime()->format('Y-m-d H:i:s'));
    }
}
