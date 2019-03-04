<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\Adapters\LocalStorage;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\FileInterface;

class UpdateFileTest extends FileTestCase
{
    public function test_it_can_update_the_contents_of_a_file()
    {
        $this->filesystem->setAdapter(new LocalStorage(__DIR__.'/../storage'));

        $directory = Directory::hydrate($this->root . '/images');
        $file = (new File)->setName('test.txt')->setParentDirectory($directory);

        $file->setContent('updated test');

        $result = $this->filesystem->updateFile($file);

        $this->assertInstanceOf(FileInterface::class, $result);
        $this->assertEquals(date('Y-m-d H:i:s'), $result->getModifiedTime()->format('Y-m-d H:i:s'));
    }
}
