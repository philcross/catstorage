<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use PHPUnit\Framework\TestCase;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileSystem;
use Tsc\CatStorageSystem\DirectoryInterface;

class DirectoryTestCase extends TestCase
{
    /** @var string */
    protected $root;

    /** @var FileSystem */
    protected $filesystem;

    protected function assertRecentlyCreatedDirectory(DirectoryInterface $directory, $path)
    {
        $this->assertTrue(is_dir($directory->getPath()));
        $this->assertEquals(date('Y-m-d H:i:s'), $directory->getCreatedTime()->format('Y-m-d H:i:s'));
        $this->assertDirectoryEquals($directory, $path);
    }

    protected function assertDirectoryEquals(DirectoryInterface $directory, $path)
    {
        $this->assertEquals(basename($path), $directory->getName());
        $this->assertEquals($path, $directory->getPath());
    }

    protected function setUp()
    {
        parent::setUp();

        mkdir(__DIR__.'/../storage');
        mkdir(__DIR__.'/../storage/images/cats', 0777, true);
        mkdir(__DIR__.'/../storage/images/dogs', 0777, true);

        file_put_contents(__DIR__.'/../storage/images/cats/test.txt', 'test');

        $this->root       = realpath(__DIR__.'/../storage');
        $this->filesystem = new FileSystem(Directory::hydrate($this->root));
    }

    protected function tearDown()
    {
        parent::tearDown();

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(__DIR__.'/../storage', \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            if ($fileinfo->isDir()) {
                rmdir($fileinfo->getRealPath());
            } else {
                unlink($fileinfo->getRealPath());
            }
        }

        rmdir(__DIR__.'/../storage');
    }
}
