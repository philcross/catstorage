<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Exceptions\DirectoryMustBeWithinRootException;
use Tsc\CatStorageSystem\Exceptions\FileAlreadyExistsException;
use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileInterface;

class RenameFileTest extends FileTestCase
{
    public function test_it_can_rename_a_file()
    {
        $directory = Directory::hydrate($this->root . '/images');
        $file = (new File)->setName('test.txt')->setParentDirectory($directory);

        $result = $this->filesystem->renameFile($file, 'testing.txt');

        $this->assertInstanceOf(FileInterface::class, $result);
        $this->assertEquals('testing.txt', $result->getName());
        $this->assertEquals($this->root . '/images/testing.txt', $result->getPath());

        $this->assertTrue(file_exists($this->root . '/images/testing.txt'));
        $this->assertFalse(file_exists($this->root . '/images/test.txt'));
    }

    public function test_an_exception_is_thrown_if_the_file_to_rename_to_already_exists()
    {
        file_put_contents($this->root . '/images/testing.txt', '');

        $directory = Directory::hydrate($this->root . '/images');
        $file = (new File)->setName('testing.txt')->setParentDirectory($directory);

        $this->expectException(FileAlreadyExistsException::class);

        $this->filesystem->renameFile($file, 'testing.txt');
    }
}
