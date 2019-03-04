<?php

namespace Tsc\CatStorageSystem\Tests;

use Mockery\MockInterface;
use Tsc\CatStorageSystem\File;
use PHPUnit\Framework\TestCase;
use Tsc\CatStorageSystem\Directory;
use Tsc\CatStorageSystem\FileSystem;
use Tsc\CatStorageSystem\FileInterface;
use Tsc\CatStorageSystem\DirectoryInterface;
use Tsc\CatStorageSystem\FileSystemInterface;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;

class FileSystemInterfaceTest extends TestCase
{
    /** @var MockInterface|AdapterInterface */
    private $adapter;

    /** @var FileSystemInterface */
    private $filesystem;

    public function test_it_creates_a_new_instance()
    {
        $stub = $this->createMock(FileSystemInterface::class);
        $this->assertTrue($stub instanceof FileSystemInterface);
    }

    public function test_it_can_create_a_new_directory()
    {
        $this->adapter->shouldReceive('createDirectory')->once()->with('/storage/files')->andReturn([
            'name'     => 'files',
            'pathname' => '/storage/files',
            'basename' => '/storage',
            'created'  => date('Y-m-d H:i:s'),
        ]);

        $directory = $this->filesystem->createDirectory(
            Directory::toCreate('/files'),
            Directory::hydrate('/storage')
        );

        $this->assertDirectory($directory, '/storage/files');
    }

    public function test_it_can_create_a_new_file()
    {
        $this->adapter->shouldReceive('createFile')->once()->with('/images/cat_1.txt', 'here be catz')->andReturn([
            'name'     => 'cat_1.txt',
            'pathname' => '/images/cat_1.txt',
            'basename' => '/images',
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => 12,
        ]);

        $create = File::toCreate(Directory::hydrate('/images'), 'cat_1.txt', 'here be catz');

        $image = $this->filesystem->createFile($create, Directory::hydrate('/images'));

        $this->assertFile($image, '/images/cat_1.txt', 12);
    }

    public function test_it_can_delete_an_existing_directory()
    {
        $this->adapter->shouldReceive('deleteDirectory')->once()->with('/images/cats')->andReturn(true);

        $result = $this->filesystem->deleteDirectory(Directory::hydrate('/images/cats'));

        $this->assertTrue($result);
    }

    public function test_it_can_delete_a_file()
    {
        $this->adapter->shouldReceive('deleteFile')->once()->with('/images/grumpy_cat.gif')->andReturn(true);

        $file = (new File)->setName('grumpy_cat.gif')->setParentDirectory(Directory::hydrate('/images'));

        $result = $this->filesystem->deleteFile($file);

        $this->assertTrue($result);
    }

    public function test_it_can_return_an_array_of_sub_directories()
    {
        $this->adapter->shouldReceive('listDirectories')->once()->with('/images')->andReturn([
            [
                'name'     => 'cats',
                'pathname' => '/images/cats',
                'basename' => '/images',
                'created'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'     => 'lions',
                'pathname' => '/images/lions',
                'basename' => '/images',
                'created'  => date('Y-m-d H:i:s'),
            ]
        ]);

        /** @var DirectoryInterface[] $result */
        $result = $this->filesystem->getDirectories(Directory::hydrate('/images'));

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->assertDirectory($result[0], '/images/cats');
        $this->assertDirectory($result[1], '/images/lions');
    }

    public function test_it_can_return_the_number_of_directories_in_a_given_directory()
    {
        $this->adapter->shouldReceive('listDirectories')->once()->with('/images')->andReturn([[], []]);

        $count = $this->filesystem->getDirectoryCount(Directory::hydrate('/images'));

        $this->assertEquals(2, $count);
    }

    public function test_it_can_return_the_size_of_the_directory()
    {
        $this->adapter->shouldReceive('getDirectorySize')->once()->with('/images')->andReturn(10);

        $size = $this->filesystem->getDirectorySize(Directory::hydrate('/images'));

        $this->assertEquals(10, $size);
    }

    public function test_it_can_return_an_array_of_files_within_a_directory()
    {
        $this->adapter->shouldReceive('listFiles')->once()->with('/images')->andReturn([
            [
                'name'     => 'grumpy_cat.gif',
                'pathname' => '/images/grumpy_cat.gif',
                'basename' => '/images',
                'created'  => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
                'size'     => 10,
            ],
            [
                'name'     => 'fat_cat.gif',
                'pathname' => '/images/fat_cat.gif',
                'basename' => '/images',
                'created'  => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
                'size'     => 20,
            ]
        ]);

        /** @var FileInterface[] $files */
        $files = $this->filesystem->getFiles(Directory::hydrate('/images'));

        $this->assertInternalType('array', $files);
        $this->assertCount(2, $files);

        $this->assertFile($files[0], '/images/grumpy_cat.gif', 10);
        $this->assertFile($files[1], '/images/fat_cat.gif', 20);
    }

    public function test_it_can_return_the_number_of_files_in_a_directory()
    {
        $this->adapter->shouldReceive('listFiles')->once()->with('/images')->andReturn([[], []]);

        $count = $this->filesystem->getFileCount(Directory::hydrate('/images'));

        $this->assertEquals(2, $count);
    }

    public function test_an_existing_directory_can_be_renamed()
    {
        $this->adapter->shouldReceive('renameDirectory')->once()->with('/images', 'img')->andReturn([
            'name'     => 'img',
            'pathname' => '/img',
            'basename' => '/',
            'created'  => date('Y-m-d H:i:s'),
        ]);

        $result = $this->filesystem->renameDirectory(Directory::hydrate('/images'), 'img');

        $this->assertDirectory($result, '/img');
    }

    public function test_it_can_rename_a_file()
    {
        $this->adapter->shouldReceive('renameFile')->once()->with('/images/meh_cat.gif', '/images/happy_cat.gif')->andReturn([
            'name'     => 'happy_cat.gif',
            'pathname' => '/images/happy_cat.gif',
            'basename' => '/images',
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => 10,
        ]);

        $this->adapter->shouldReceive('getDirectory')->once()->with('/images')->andReturn([
            'name'     => 'images',
            'pathname' => '/images',
            'basename' => '/',
            'created'  => date('Y-m-d H:i:s'),
        ]);

        $file = (new File)->setName('meh_cat.gif')->setParentDirectory(Directory::hydrate('/images'));

        $result = $this->filesystem->renameFile($file, '/images/happy_cat.gif');

        $this->assertFile($result, '/images/happy_cat.gif');
    }

    public function test_it_can_update_the_contents_of_a_file()
    {
        $this->adapter->shouldReceive('updateFile')->once()->with('/images/test.txt', 'updated test')->andReturn([
            'name'     => 'test.txt',
            'pathname' => '/images/test.txt',
            'basename' => '/images',
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => 10,
        ]);

        $this->adapter->shouldReceive('getDirectory')->once()->with('/images')->andReturn([
            'name'     => 'images',
            'pathname' => '/images',
            'basename' => '/',
            'created'  => date('Y-m-d H:i:s'),
        ]);

        $file = (new File)->setName('test.txt')->setParentDirectory(Directory::hydrate('/images'));

        $file->setContent('updated test');

        $result = $this->filesystem->updateFile($file);

        $this->assertFile($result, '/images/test.txt', 10);
    }

    private function assertDirectory(DirectoryInterface $directory, $path)
    {
        $this->assertEquals(basename($path), $directory->getName());
        $this->assertEquals($path, $directory->getPath());
        $this->assertEquals(date('Y-m-d H:i:s'), $directory->getCreatedTime()->format('Y-m-d H:i:s'));
    }

    private function assertFile(FileInterface $file, $path, $size = null)
    {
        $this->assertEquals(basename($path), $file->getName());
        $this->assertEquals($path, $file->getPath());
        $this->assertEquals(date('Y-m-d H:i:s'), $file->getCreatedTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(date('Y-m-d H:i:s'), $file->getModifiedTime()->format('Y-m-d H:i:s'));

        if (!is_null($size)) {
            $this->assertEquals($size, $file->getSize());
        }
    }

    protected function setUp()
    {
        parent::setUp();

        $this->adapter    = \Mockery::mock(AdapterInterface::class);
        $this->filesystem = new FileSystem($this->adapter);
    }
}
