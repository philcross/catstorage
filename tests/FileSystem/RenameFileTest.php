<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileInterface;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;

class RenameFileTest extends FileTestCase
{
    public function test_it_can_rename_a_file()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('renameFile')->once()->with('/images/meh_cat.gif', '/images/happy_cat.gif')->andReturn([
            'name'     => 'happy_cat.gif',
            'pathname' => '/images/happy_cat.gif',
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

        $this->filesystem->setAdapter($adapter);

        $file = (new File)->setName('meh_cat.gif')->setParentDirectory(Directory::hydrate('/images'));

        $result = $this->filesystem->renameFile($file, '/images/happy_cat.gif');

        $this->assertInstanceOf(FileInterface::class, $result);
        $this->assertEquals('happy_cat.gif', $result->getName());
        $this->assertEquals('/images/happy_cat.gif', $result->getPath());
    }
}
