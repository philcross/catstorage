<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use PHPUnit\Framework\TestCase;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileSystem;

class FileTestCase extends TestCase
{
    /** @var string */
    protected $root;

    protected function setUp()
    {
        parent::setUp();

        mkdir(__DIR__.'/../storage');
        mkdir(__DIR__.'/../storage/images', 0777, true);

        file_put_contents(__DIR__.'/../storage/images/test.txt', 'test');

        $this->root       = realpath(__DIR__.'/../storage');
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
