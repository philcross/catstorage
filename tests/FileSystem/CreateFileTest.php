<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Exceptions\DirectoryMustBeWithinRootException;
use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\FileInterface;

class CreateFileTest extends FileTestCase
{
    public function test_it_can_create_a_new_file()
    {
        $content = file_get_contents(__DIR__ . '/../../images/cat_1.gif');

        $file = (new File)->setName('cat_1.gif')->setContent($content);

        $directory = Directory::hydrate($this->root . '/images');

        $image = $this->filesystem->createFile($file, $directory);

        $this->assertInstanceOf(FileInterface::class, $image);
        $this->assertEquals('cat_1.gif', $image->getName());
        $this->assertEquals(realpath(__DIR__ . '/../storage/images/cat_1.gif'), $image->getPath());
        $this->assertEquals(date('Y-m-d H:i:s'), $image->getCreatedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(date('Y-m-d H:i:s'), $image->getModifiedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals($content, $image->getContent());

        $this->assertTrue(is_file($image->getPath()));
    }

    public function test_it_cannot_create_a_directory_outside_the_root()
    {
        $directory = Directory::hydrate($this->root . '/../invalid');

        $this->expectException(DirectoryMustBeWithinRootException::class);

        $this->filesystem->createFile(new File, $directory);
    }

    public function test_it_creates_the_parent_directory_if_it_doesnt_already_exist()
    {
        $content = file_get_contents(__DIR__ . '/../../images/cat_1.gif');

        $file = (new File)->setName('cat_1.gif')->setContent($content);

        $directory = Directory::hydrate($this->root . '/images/cats');

        $image = $this->filesystem->createFile($file, $directory);

        $this->assertInstanceOf(FileInterface::class, $image);
        $this->assertEquals('cat_1.gif', $image->getName());
        $this->assertEquals(realpath(__DIR__ . '/../storage/images/cats/cat_1.gif'), $image->getPath());
        $this->assertEquals(date('Y-m-d H:i:s'), $image->getCreatedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(date('Y-m-d H:i:s'), $image->getModifiedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals($content, $image->getContent());

        $this->assertTrue(is_file($image->getPath()));
    }
}
