<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileSystem;
use Tsc\CatStorageSystem\Exceptions\RootDirectoryNotDefinedException;

class CreateDirectoryTest extends DirectoryTestCase
{
    public function test_it_can_create_a_root_directory()
    {
        $filesystem = new FileSystem;

        $root = (new Directory)->setName('images')->setPath($this->root . '/images');

        $directory = $filesystem->createRootDirectory($root);

        $this->assertTrue(is_dir($this->root . '/images'));
        $this->assertEquals('images', $directory->getName());
        $this->assertEquals(date('Y-m-d H:i:s'), $directory->getCreatedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals($this->root . '/images', $directory->getPath());
    }

    public function test_it_can_create_a_new_directory()
    {
        $root = (new Directory)->setName('')->setCreatedTime(new \DateTime)->setPath($this->root);
        $create = (new Directory)->setName('images');

        $filesystem = new FileSystem($root);

        $directory = $filesystem->createDirectory($create, $root);

        $this->assertTrue(is_dir($this->root . '/images'));
        $this->assertEquals('images', $directory->getName());
        $this->assertEquals(date('Y-m-d H:i:s'), $directory->getCreatedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals($this->root . '/images', $directory->getPath());
    }

    public function test_it_throws_an_exception_when_trying_to_create_a_directory_without_a_root_being_defined()
    {
        $this->expectException(RootDirectoryNotDefinedException::class);

        $filesystem = new FileSystem;

        $filesystem->createDirectory(new Directory, new Directory);
    }

    public function test_it_returns_the_directory_object_if_the_root_already_exists_when_trying_to_create_it()
    {
        $filesystem = new FileSystem;

        $root = (new Directory)->setName('')->setPath($this->root);

        $directory = $filesystem->createRootDirectory($root);

        $this->assertTrue(is_dir($this->root));
        $this->assertEquals('', $directory->getName());
        $this->assertEquals(date('Y-m-d H:i:s'), $directory->getCreatedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals($this->root, $directory->getPath());
    }
}
