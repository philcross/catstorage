<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Exceptions\DirectoryMustBeWithinRootException;
use Tsc\CatStorageSystem\FileInterface;

class GetFilesTest extends FileTestCase
{
    public function test_it_can_return_an_array_of_files_within_a_directory()
    {
        $directory = Directory::hydrate($this->root . '/images');

        $files = $this->filesystem->getFiles($directory);

        $this->assertInternalType('array', $files);
        $this->assertCount(1, $files);

        $this->assertInstanceOf(FileInterface::class, $files[0]);
        $this->assertEquals($this->root . '/images/test.txt', $files[0]->getPath());
    }

    public function test_it_throws_an_exception_if_the_directory_getting_files_for_is_outside_the_root()
    {
        $directory = Directory::hydrate($this->root . '/../invalid');

        $this->expectException(DirectoryMustBeWithinRootException::class);

        $this->filesystem->getFiles($directory);
    }

    public function test_it_can_return_the_number_of_files_in_a_directory()
    {
        $directory = Directory::hydrate($this->root . '/images');

        $this->assertEquals(1, $this->filesystem->getFileCount($directory));
    }
}
