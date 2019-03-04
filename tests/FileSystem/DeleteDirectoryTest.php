<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Adapters\LocalStorage;

class DeleteDirectoryTest extends DirectoryTestCase
{
    public function test_it_can_delete_an_existing_directory()
    {
        $this->filesystem->setAdapter(new LocalStorage(__DIR__.'/../storage'));
        $delete = Directory::hydrate($this->root.'/images/cats');

        $result = $this->filesystem->deleteDirectory($delete);

        $this->assertTrue($result);
        $this->assertFalse(is_dir($delete->getPath()));
    }
}
