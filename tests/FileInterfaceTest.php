<?php

namespace Tsc\CatStorageSystem\Tests;

use DateTime;
use Tsc\CatStorageSystem\File;
use PHPUnit\Framework\TestCase;
use Tsc\CatStorageSystem\FileInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Tsc\CatStorageSystem\DirectoryInterface;

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
        $parentDirectory->method('getName')->willReturn('test_directory');
        $parentDirectory->method('getCreatedTime')->willReturn(new DateTime('2019-03-02 00:00:00'));
        $parentDirectory->method('getPath')->willReturn('/test_directory');

        $file = (new File)
            ->setName('test.txt')
            ->setSize(1000)
            ->setCreatedTime(new DateTime('2019-03-02 00:00:00'))
            ->setModifiedTime(new DateTime('2019-03-02 01:00:00'))
            ->setParentDirectory($parentDirectory);

        $this->assertEquals('test.txt', $file->getName());
        $this->assertEquals(1000, $file->getSize());
        $this->assertEquals(new DateTime('2019-03-02 00:00:00'), $file->getCreatedTime());
        $this->assertEquals(new DateTime('2019-03-02 01:00:00'), $file->getModifiedTime());
        $this->assertEquals($parentDirectory, $file->getParentDirectory());
    }
}
