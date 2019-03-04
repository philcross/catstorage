<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileInterface;
use Tsc\CatStorageSystem\Adapters\LocalStorage;

class CreateFileTest extends FileTestCase
{
    public function test_it_can_create_a_new_file()
    {
        $content = file_get_contents(__DIR__ . '/../../images/cat_1.gif');
        $this->filesystem->setAdapter(new LocalStorage(__DIR__.'/../storage'));

        $file = (new File)->setName('cat_1.gif')->setContent($content);

        $directory = Directory::hydrate($this->root . '/images');

        $image = $this->filesystem->createFile($file, $directory);

        $this->assertInstanceOf(FileInterface::class, $image);
        $this->assertEquals('cat_1.gif', $image->getName());
        $this->assertEquals(realpath(__DIR__ . '/../storage/images/cat_1.gif'), $image->getPath());
        $this->assertEquals(date('Y-m-d H:i:s'), $image->getCreatedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(date('Y-m-d H:i:s'), $image->getModifiedTime()->format('Y-m-d H:i:s'));

        $this->assertTrue(is_file($image->getPath()));
    }
}
