<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use Tsc\CatStorageSystem\File;
use Tsc\CatStorageSystem\Directory;

class DeleteFileTest extends FileTestCase
{
    public function test_it_can_delete_a_file()
    {
        file_put_contents($this->root . '/images/to_delete.txt', '');

        $directory = Directory::hydrate($this->root . '/images');
        $file = (new File)->setName('to_delete.txt')->setParentDirectory($directory);

        $result = $this->filesystem->deleteFile($file);

        $this->assertTrue($result);

        $this->assertFalse(file_exists($this->root . '/images/to_delete.txt'));
    }

    public function test_it_returns_true_when_deleting_a_file_that_doesnt_already_exist()
    {
        $directory = Directory::hydrate($this->root . '/images');
        $file = (new File)->setName('invalid.txt')->setParentDirectory($directory);

        $result = $this->filesystem->deleteFile($file);

        $this->assertTrue($result);
    }
}
