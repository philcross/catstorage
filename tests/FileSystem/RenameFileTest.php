<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Adapters\LocalStorage;
use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileInterface;

class RenameFileTest extends FileTestCase
{
    public function test_it_can_rename_a_file()
    {
        $this->filesystem->setAdapter(new LocalStorage(__DIR__.'/../storage'));

        $directory = Directory::hydrate($this->root . '/images');
        $file = (new File)->setName('test.txt')->setParentDirectory($directory);

        $result = $this->filesystem->renameFile($file, 'testing.txt');

        $this->assertInstanceOf(FileInterface::class, $result);
        $this->assertEquals('testing.txt', $result->getName());
        $this->assertEquals($this->root . '/testing.txt', $result->getPath());

        $this->assertTrue(file_exists($this->root . '/testing.txt'));
        $this->assertFalse(file_exists($this->root . '/images/test.txt'));
    }
}
