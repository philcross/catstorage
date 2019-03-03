<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Exceptions\DirectoryMustBeWithinRootException;

class DeleteDirectoryTest extends DirectoryTestCase
{
    public function test_it_can_delete_an_existing_directory()
    {
        $delete = Directory::hydrate($this->root.'/images/cats');

        $result = $this->filesystem->deleteDirectory($delete);

        $this->assertTrue($result);
        $this->assertFalse(is_dir($delete->getPath()));
    }

    public function test_it_returns_true_if_the_directory_doesnt_exist()
    {
        $delete = Directory::hydrate($this->root.'/invalid');

        $result = $this->filesystem->deleteDirectory($delete);

        $this->assertTrue($result);
    }

    public function test_it_cannot_delete_directories_higher_than_the_root()
    {
        $delete = Directory::hydrate($this->root.'/../invalid');

        $this->expectException(DirectoryMustBeWithinRootException::class);

        $this->filesystem->deleteDirectory($delete);
    }
}
