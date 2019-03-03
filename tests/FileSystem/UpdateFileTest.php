<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Exceptions\DirectoryMustBeWithinRootException;
use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\FileInterface;

class UpdateFileTest extends FileTestCase
{
    public function test_it_can_update_the_contents_of_a_file()
    {
        $directory = Directory::hydrate($this->root . '/images');
        $file = (new File)->setName('test.txt')->setParentDirectory($directory);

        $file->setContent('updated test');

        $result = $this->filesystem->updateFile($file);

        $this->assertInstanceOf(FileInterface::class, $result);
        $this->assertEquals('updated test', file_get_contents($file->getPath()));
        $this->assertEquals(date('Y-m-d H:i:s'), $file->getModifiedTime()->format('Y-m-d H:i:s'));
    }

    public function test_it_creates_the_file_if_doesnt_exist_when_trying_to_update()
    {
        $directory = Directory::hydrate($this->root . '/images');
        $file = (new File)->setName('missing.txt')->setParentDirectory($directory);

        $file->setContent('updated test');

        $result = $this->filesystem->updateFile($file);

        $this->assertInstanceOf(FileInterface::class, $result);
        $this->assertEquals(date('Y-m-d H:i:s'), $result->getCreatedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(date('Y-m-d H:i:s'), $result->getModifiedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(12, $result->getSize());
        $this->assertEquals('updated test', file_get_contents($result->getPath()));

        $this->assertTrue(file_exists($result->getPath()));
    }

    public function test_it_throws_an_exception_if_the_file_is_outside_the_root_directory()
    {
        $directory = Directory::hydrate($this->root . '/../invalid');
        $file = (new File)->setName('missing.txt')->setParentDirectory($directory);

        $this->expectException(DirectoryMustBeWithinRootException::class);

        $this->filesystem->updateFile($file);
    }
}
