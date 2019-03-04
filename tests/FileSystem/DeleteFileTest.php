<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;

class DeleteFileTest extends FileTestCase
{
    public function test_it_can_delete_a_file()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('deleteFile')->once()->with('/images/grumpy_cat.gif')->andReturn(true);

        $this->filesystem->setAdapter($adapter);

        $file = (new File)->setName('grumpy_cat.gif')->setParentDirectory(Directory::hydrate('/images'));

        $result = $this->filesystem->deleteFile($file);

        $this->assertTrue($result);
    }
}
