<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;

class DeleteDirectoryTest extends DirectoryTestCase
{
    public function test_it_can_delete_an_existing_directory()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('deleteDirectory')->once()->with('/images/cats')->andReturn(true);

        $this->filesystem->setAdapter($adapter);
        $delete = Directory::hydrate('/images/cats');

        $result = $this->filesystem->deleteDirectory($delete);

        $this->assertTrue($result);
        $this->assertFalse(is_dir($delete->getPath()));
    }
}
