<?php

namespace Tsc\CatStorageSystem\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Tsc\CatStorageSystem\Models\File;
use PHPUnit\Framework\MockObject\MockObject;
use Tsc\CatStorageSystem\Models\FileInterface;
use Tsc\CatStorageSystem\Models\DirectoryInterface;

class FileInterfaceTest extends TestCase
{
    public function test_it_creates_a_new_instance()
    {
        $stub = $this->createMock(FileInterface::class);
        $this->assertTrue($stub instanceof FileInterface);
    }

    public function test_it_can_set_and_return_the_file_attributes()
    {
        /** @var DirectoryInterface|MockObject $parentDirectory */
        $parentDirectory = $this->createMock(DirectoryInterface::class);
        $parentDirectory->method('getPath')->willReturn('/test_directory');

        $file = (new File)
            ->setName('test.txt')
            ->setSize(1000)
            ->setCreatedTime(new DateTime('2019-03-02 00:00:00'))
            ->setModifiedTime(new DateTime('2019-03-02 01:00:00'))
            ->setContent('test')
            ->setParentDirectory($parentDirectory);

        $this->assertEquals('test.txt', $file->getName());
        $this->assertEquals(1000, $file->getSize());
        $this->assertEquals(new DateTime('2019-03-02 00:00:00'), $file->getCreatedTime());
        $this->assertEquals(new DateTime('2019-03-02 01:00:00'), $file->getModifiedTime());
        $this->assertEquals('test', $file->getContent());
        $this->assertEquals($parentDirectory, $file->getParentDirectory());
    }

    public function test_it_can_create_a_new_file()
    {
        $parent = $parentDirectory = $this->createMock(DirectoryInterface::class);
        $parentDirectory->method('getPath')->willReturn('/test_directory');

        $file = File::toCreate($parent, 'test.txt', 'This is a test');

        $this->assertEquals('test.txt', $file->getName());
        $this->assertEquals('/test_directory/test.txt', $file->getPath());
        $this->assertEquals(0, $file->getSize());
        $this->assertNull($file->getCreatedTime());
        $this->assertNull($file->getModifiedTime());
        $this->assertEquals($parent, $file->getParentDirectory());
    }
}
