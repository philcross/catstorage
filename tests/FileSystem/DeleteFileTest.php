<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;
use Tsc\CatStorageSystem\FileSystem;

class DeleteFileTest extends FileTestCase
{
    public function test_it_can_delete_a_file()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('deleteFile')->once()->with('/images/grumpy_cat.gif')->andReturn(true);

        $filesystem = new FileSystem($adapter);

        $file = (new File)->setName('grumpy_cat.gif')->setParentDirectory(Directory::hydrate('/images'));

        $result = $filesystem->deleteFile($file);

        $this->assertTrue($result);
    }
}
