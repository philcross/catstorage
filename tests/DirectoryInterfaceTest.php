<?php

namespace Tsc\CatStorageSystem\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Tsc\CatStorageSystem\Models\Directory;
use Tsc\CatStorageSystem\Models\DirectoryInterface;

class DirectoryInterfaceTest extends TestCase
{
    public function test_it_creates_a_new_instance()
    {
        $stub = $this->createMock(DirectoryInterface::class);
        $this->assertTrue($stub instanceof DirectoryInterface);
    }

    public function test_it_can_set_and_return_the_directory_attributes()
    {
        $directory = (new Directory)
            ->setName('test_directory')
            ->setCreatedTime(new DateTime('2019-03-02 00:00:00'))
            ->setPath('/test_directory');

        $this->assertEquals('test_directory', $directory->getName());
        $this->assertEquals(new DateTime('2019-03-02 00:00:00'), $directory->getCreatedTime());
        $this->assertEquals('/test_directory', $directory->getPath());
    }

    public function test_it_can_create_a_new_directory()
    {
        $directory = Directory::toCreate('/files/images');

        $this->assertEquals('images', $directory->getName());
        $this->assertEquals('/files/images', $directory->getPath());
        $this->assertNull($directory->getCreatedTime());
    }
}
