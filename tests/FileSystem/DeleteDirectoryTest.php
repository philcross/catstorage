<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use DateTime;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileSystem;
use Tsc\CatStorageSystem\Exceptions\CannotDeleteDirectoryOutsideRootException;

class DeleteDirectoryTest extends DirectoryTestCase
{
    public function test_it_can_delete_an_existing_directory()
    {
        mkdir($this->root . '/images/cats', 0777, true);

        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $delete = (new Directory)->setName('cats')->setCreatedTime(new DateTime)->setPath($this->root . '/images/cats');

        $filesystem = new FileSystem($root);

        $result = $filesystem->deleteDirectory($delete);

        $this->assertTrue($result);

        $this->assertFalse(is_dir($delete->getPath()));
    }

    public function test_it_returns_true_if_the_directory_doesnt_exist()
    {
        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $delete = (new Directory)->setName('invalid')->setCreatedTime(new DateTime)->setPath($this->root . '/invalid');

        $filesystem = new FileSystem($root);

        $result = $filesystem->deleteDirectory($delete);

        $this->assertTrue($result);
    }

    public function test_it_cannot_delete_directories_higher_than_the_root()
    {
        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $delete = (new Directory)->setName('invalid')->setCreatedTime(new DateTime)->setPath($this->root . '/../invalid');

        $filesystem = new FileSystem($root);

        $this->expectException(CannotDeleteDirectoryOutsideRootException::class);

        $filesystem->deleteDirectory($delete);
    }
}
