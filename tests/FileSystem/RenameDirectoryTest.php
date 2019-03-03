<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\DirectoryInterface;
use Tsc\CatStorageSystem\Exceptions\DirectoryMustBeWithinRootException;

class RenameDirectoryTest extends DirectoryTestCase
{
    public function test_an_existing_directory_can_be_renamed()
    {
        $rename = Directory::hydrate($this->root.'/images');

        $result = $this->filesystem->renameDirectory($rename, 'img');

        $this->assertInstanceOf(DirectoryInterface::class, $result);

        $this->assertDirectoryEquals($result, $this->root.'/img');

        $this->assertFalse(is_dir($rename->getPath()));
        $this->assertTrue(is_dir($result->getPath()));
    }

    public function test_the_new_name_can_be_a_path()
    {
        $rename = Directory::hydrate($this->root.'/images');

        $result = $this->filesystem->renameDirectory($rename, 'img/cats');

        $this->assertInstanceOf(DirectoryInterface::class, $result);

        $this->assertDirectoryEquals($result, $this->root.'/img/cats');

        $this->assertFalse(is_dir($rename->getPath()));
        $this->assertTrue(is_dir($result->getPath()));
    }

    public function test_it_cannot_move_the_directory_outside_the_root()
    {
        $rename = Directory::hydrate($this->root.'/images');

        $this->expectException(DirectoryMustBeWithinRootException::class);

        $this->filesystem->renameDirectory($rename, '/../invalid');
    }
}
