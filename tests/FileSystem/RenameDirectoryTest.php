<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Adapters\LocalStorage;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\DirectoryInterface;

class RenameDirectoryTest extends DirectoryTestCase
{
    public function test_an_existing_directory_can_be_renamed()
    {
        $this->filesystem->setAdapter(new LocalStorage(__DIR__.'/../storage'));

        $rename = Directory::hydrate($this->root.'/images');

        $result = $this->filesystem->renameDirectory($rename, 'img');

        $this->assertInstanceOf(DirectoryInterface::class, $result);

        $this->assertDirectoryEquals($result, $this->root.'/img');

        $this->assertFalse(is_dir($rename->getPath()));
        $this->assertTrue(is_dir($result->getPath()));
    }
}
