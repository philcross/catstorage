<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem;

use PHPUnit\Framework\TestCase;

class DirectoryTestCase extends TestCase
{
    protected $root;

    protected function setUp()
    {
        parent::setUp();

        mkdir(__DIR__ . '/../storage');

        $this->root = realpath(__DIR__ . '/../storage');
    }

    protected function tearDown()
    {
        parent::tearDown();

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(__DIR__ . '/../storage', \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            if ($fileinfo->isDir()) {
                rmdir($fileinfo->getRealPath());
            } else {
                unlink($fileinfo->getRealPath());
            }
        }

        rmdir(__DIR__ . '/../storage');
    }
}
