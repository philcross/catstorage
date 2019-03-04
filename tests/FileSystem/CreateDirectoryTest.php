<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Adapters\LocalStorage;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileSystem;
use Tsc\CatStorageSystem\Exceptions\RootDirectoryNotDefinedException;

class CreateDirectoryTest extends DirectoryTestCase
{
    public function test_it_can_create_a_root_directory()
    {
        $filesystem = new FileSystem;

        $root = (new Directory)->setName('files')->setPath($this->root.'/files');

        $directory = $filesystem->createRootDirectory($root);

        $this->assertRecentlyCreatedDirectory($directory, $this->root.'/files');
    }

    public function test_it_can_create_a_new_directory()
    {
        $root   = Directory::hydrate($this->root);
        $create = (new Directory)->setName('files');

        $filesystem = new FileSystem($root);
        $filesystem->setAdapter(new LocalStorage(__DIR__.'/../storage'));

        $directory = $filesystem->createDirectory($create, $root);

        $this->assertRecentlyCreatedDirectory($directory, $this->root.'/files');
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

        $root = Directory::hydrate($this->root);

        $directory = $filesystem->createRootDirectory($root);

        $this->assertRecentlyCreatedDirectory($directory, $this->root);
    }
}
