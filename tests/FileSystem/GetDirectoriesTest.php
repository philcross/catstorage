<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Adapters\LocalStorage;

class GetDirectoriesTest extends DirectoryTestCase
{
    public function test_it_can_return_an_array_of_sub_directories()
    {
        $this->filesystem->setAdapter(new LocalStorage(__DIR__.'/../storage'));

        $images = Directory::hydrate($this->root.'/images');

        $result = $this->filesystem->getDirectories($images);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        usort($result, function($a, $b) {
            return $a->getName() <=> $b->getName();
        });

        $this->assertDirectoryEquals($result[0], $this->root.'/images/cats');
        $this->assertDirectoryEquals($result[1], $this->root.'/images/dogs');
    }

    public function test_it_can_return_the_number_of_directories_in_a_given_directory()
    {
        $this->filesystem->setAdapter(new LocalStorage(__DIR__.'/../storage'));

        $images = Directory::hydrate($this->root.'/images');

        $this->assertEquals(2, $this->filesystem->getDirectoryCount($images));
    }

    public function test_it_can_return_the_size_of_the_directory()
    {
        $this->filesystem->setAdapter(new LocalStorage(__DIR__.'/../storage'));

        $images = Directory::hydrate($this->root.'/images');

        $this->assertEquals(4, $this->filesystem->getDirectorySize($images));
    }
}
