<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\Adapters\LocalStorage;

class DeleteFileTest extends FileTestCase
{
    public function test_it_can_delete_a_file()
    {
        $this->filesystem->setAdapter(new LocalStorage(__DIR__.'/../storage'));

        file_put_contents($this->root . '/images/to_delete.txt', '');

        $directory = Directory::hydrate($this->root . '/images');
        $file = (new File)->setName('to_delete.txt')->setParentDirectory($directory);

        $result = $this->filesystem->deleteFile($file);

        $this->assertTrue($result);

        $this->assertFalse(file_exists($this->root . '/images/to_delete.txt'));
    }
}
