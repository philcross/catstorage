<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileSystem;
use Tsc\CatStorageSystem\Adapters\LocalStorage;

class CreateDirectoryTest extends DirectoryTestCase
{
    /**
     * @deprecated
     */
    public function it_can_create_a_root_directory()
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
}
