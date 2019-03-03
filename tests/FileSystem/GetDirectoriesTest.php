<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use DateTime;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileSystem;
use Tsc\CatStorageSystem\Exceptions\DirectoryMustBeWithinRootException;

class GetDirectoriesTest extends DirectoryTestCase
{
    public function test_it_can_return_an_array_of_sub_directories()
    {
        mkdir($this->root . '/images/cats', 0777, true);
        mkdir($this->root . '/images/dogs', 0777, true);

        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $images = (new Directory)->setName('images')->setCreatedTime(new DateTime)->setPath($this->root . '/images');

        $filesystem = new FileSystem($root);

        $result = $filesystem->getDirectories($images);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->assertEquals('cats', $result[0]->getName());
        $this->assertEquals($this->root . '/images/cats', $result[0]->getPath());

        $this->assertEquals('dogs', $result[1]->getName());
        $this->assertEquals($this->root . '/images/dogs', $result[1]->getPath());
    }

    public function test_it_can_return_the_number_of_directories_in_a_given_directory()
    {
        mkdir($this->root . '/images/cats', 0777, true);
        mkdir($this->root . '/images/dogs', 0777, true);

        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $images = (new Directory)->setName('images')->setCreatedTime(new DateTime)->setPath($this->root . '/images');

        $filesystem = new FileSystem($root);

        $this->assertEquals(2, $filesystem->getDirectoryCount($images));
    }

    public function test_it_can_return_the_size_of_the_directory()
    {
        mkdir($this->root . '/images/cats', 0777, true);
        file_put_contents($this->root . '/images/cats/test.txt', 'test');

        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $images = (new Directory)->setName('images')->setCreatedTime(new DateTime)->setPath($this->root . '/images');

        $filesystem = new FileSystem($root);

        $this->assertEquals(4, $filesystem->getDirectorySize($images));
    }

    public function test_it_cannot_get_directories_outside_the_root()
    {
        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $invalid = (new Directory)->setName('invalid')->setCreatedTime(new DateTime)->setPath($this->root . '/../invalid');

        $filesystem = new FileSystem($root);

        $this->expectException(DirectoryMustBeWithinRootException::class);

        $filesystem->getDirectories($invalid);
    }

    public function test_it_cannot_count_directories_outside_the_root()
    {
        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $invalid = (new Directory)->setName('invalid')->setCreatedTime(new DateTime)->setPath($this->root . '/../invalid');

        $filesystem = new FileSystem($root);

        $this->expectException(DirectoryMustBeWithinRootException::class);

        $filesystem->getDirectoryCount($invalid);
    }

    public function test_it_cannot_return_the_size_of_a_directory_outside_the_root()
    {
        $root = (new Directory)->setName('')->setCreatedTime(new DateTime)->setPath($this->root);
        $images = (new Directory)->setName('invalid')->setCreatedTime(new DateTime)->setPath($this->root . '/../invalid');

        $filesystem = new FileSystem($root);

        $this->expectException(DirectoryMustBeWithinRootException::class);

        $filesystem->getDirectorySize($images);
    }
}
