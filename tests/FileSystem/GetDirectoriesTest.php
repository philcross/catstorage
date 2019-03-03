<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Exceptions\DirectoryMustBeWithinRootException;

class GetDirectoriesTest extends DirectoryTestCase
{
    public function test_it_can_return_an_array_of_sub_directories()
    {
        $images = Directory::hydrate($this->root.'/images');

        $result = $this->filesystem->getDirectories($images);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->assertDirectoryEquals($result[0], $this->root.'/images/cats');
        $this->assertDirectoryEquals($result[1], $this->root.'/images/dogs');
    }

    public function test_it_can_return_the_number_of_directories_in_a_given_directory()
    {
        $images = Directory::hydrate($this->root.'/images');

        $this->assertEquals(2, $this->filesystem->getDirectoryCount($images));
    }

    public function test_it_can_return_the_size_of_the_directory()
    {
        $images = Directory::hydrate($this->root.'/images');

        $this->assertEquals(4, $this->filesystem->getDirectorySize($images));
    }

    public function test_it_cannot_get_directories_outside_the_root()
    {
        $invalid = Directory::hydrate($this->root.'/../invalid');

        $this->expectException(DirectoryMustBeWithinRootException::class);

        $this->filesystem->getDirectories($invalid);
    }

    public function test_it_cannot_count_directories_outside_the_root()
    {
        $invalid = Directory::hydrate($this->root.'/../invalid');

        $this->expectException(DirectoryMustBeWithinRootException::class);

        $this->filesystem->getDirectoryCount($invalid);
    }

    public function test_it_cannot_return_the_size_of_a_directory_outside_the_root()
    {
        $invalid = Directory::hydrate($this->root.'/../invalid');

        $this->expectException(DirectoryMustBeWithinRootException::class);

        $this->filesystem->getDirectorySize($invalid);
    }
}
