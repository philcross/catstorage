<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use DateTime;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileSystem;
use Tsc\CatStorageSystem\DirectoryInterface;
use Tsc\CatStorageSystem\Exceptions\CannotMoveDirectoryOutsideRootException;

class RenameDirectoryTest extends DirectoryTestCase
{
    public function test_an_existing_directory_can_be_renamed()
    {
        mkdir($this->root . '/images', 0777, true);

        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $rename = (new Directory)->setName('images')->setCreatedTime(new DateTime)->setPath($this->root . '/images');

        $filesystem = new FileSystem($root);

        $result = $filesystem->renameDirectory($rename, 'img');

        $this->assertInstanceOf(DirectoryInterface::class, $result);

        $this->assertEquals('img', $result->getName());
        $this->assertEquals($this->root . '/img', $result->getPath());

        $this->assertFalse(is_dir($rename->getPath()));
        $this->assertTrue(is_dir($result->getPath()));
    }

    public function test_the_new_name_can_be_a_path()
    {
        mkdir($this->root . '/images', 0777, true);

        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $rename = (new Directory)->setName('images')->setCreatedTime(new DateTime)->setPath($this->root . '/images');

        $filesystem = new FileSystem($root);

        $result = $filesystem->renameDirectory($rename, 'img/cats');

        $this->assertInstanceOf(DirectoryInterface::class, $result);

        $this->assertEquals('cats', $result->getName());
        $this->assertEquals($this->root . '/img/cats', $result->getPath());

        $this->assertFalse(is_dir($rename->getPath()));
        $this->assertTrue(is_dir($result->getPath()));
    }

    public function test_it_cannot_move_the_directory_outside_the_root()
    {
        mkdir($this->root . '/images', 0777, true);

        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $rename = (new Directory)->setName('images')->setCreatedTime(new DateTime)->setPath($this->root . '/images');

        $filesystem = new FileSystem($root);

        $this->expectException(CannotMoveDirectoryOutsideRootException::class);

        $filesystem->renameDirectory($rename, '/../invalid');
    }
}
