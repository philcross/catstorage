<?php

namespace Tsc\CatStorageSystem\Tests\FileSystem\Adapters;

use PHPUnit\Framework\TestCase;
use Tsc\CatStorageSystem\Adapters\AdapterInterface;
use Tsc\CatStorageSystem\Exceptions\PathNotInRootException;
use Tsc\CatStorageSystem\Exceptions\FileDoesntExistException;
use Tsc\CatStorageSystem\Exceptions\DirectoryDoesntExistException;
use Tsc\CatStorageSystem\Exceptions\DirectoryAlreadyExistException;

abstract class AbstractAdapterTest extends TestCase
{
    public function test_it_can_check_if_a_directory_exists()
    {
        $adapter = $this->getAdapter();

        $this->assertTrue($adapter->directoryExists($this->getRootPath().'/files'));
        $this->assertFalse($adapter->directoryExists($this->getRootPath().'/invalid'));
    }

    public function test_it_can_create_a_directory()
    {
        $adapter = $this->getAdapter();

        $info = $adapter->createDirectory('/images/cats');

        $this->assertEquals([
            'name'     => 'cats',
            'pathname' => $this->getRootPath().'/images/cats',
            'basename' => $this->getRootPath().'/images',
            'created'  => date('Y-m-d H:i:s'),
        ], $info);
    }

    public function test_it_returns_directory_information_if_the_directory_already_exists_when_creating_it()
    {
        $adapter = $this->getAdapter();

        $info = $adapter->createDirectory('/files');

        $this->assertEquals([
            'name'     => 'files',
            'pathname' => $this->getRootPath().'/files',
            'basename' => $this->getRootPath(),
            'created'  => date('Y-m-d H:i:s'),
        ], $info);
    }

    public function test_it_throws_an_exception_if_the_directory_to_create_is_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->createDirectory('/../files');
    }

    public function test_it_can_delete_a_directory()
    {
        $adapter = $this->getAdapter();

        $status = $adapter->deleteDirectory('/files');

        $this->assertTrue($status);
        $this->assertFalse($adapter->directoryExists('/files'));
    }

    public function test_it_returns_true_when_deleting_a_non_existing_directory()
    {
        $adapter = $this->getAdapter();

        $status = $adapter->deleteDirectory('/missing');

        $this->assertTrue($status);
    }

    public function test_it_throws_an_exception_if_the_directory_to_delete_is_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->deleteDirectory('/../files');
    }

    public function test_it_can_rename_a_directory()
    {
        $adapter = $this->getAdapter();

        $info = $adapter->renameDirectory('/files', '/images');

        $this->assertEquals([
            'name'     => 'images',
            'pathname' => $this->getRootPath().'/images',
            'basename' => $this->getRootPath(),
            'created'  => date('Y-m-d H:i:s'),
        ], $info);

        $this->assertTrue($adapter->directoryExists($this->getRootPath().'/images'));
        $this->assertFalse($adapter->directoryExists($this->getRootPath().'/files'));
    }

    public function test_it_throws_an_exception_if_the_directory_doesnt_exist_when_renaming_a_directory()
    {
        $adapter = $this->getAdapter();

        $this->expectException(DirectoryDoesntExistException::class);

        $adapter->renameDirectory('/invalid', '/images');
    }

    public function test_it_throws_an_exception_if_the_target_directory_already_exists()
    {
        $adapter = $this->getAdapter();
        $adapter->createDirectory('/images');

        $this->expectException(DirectoryAlreadyExistException::class);

        $adapter->renameDirectory('/files', '/images');
    }

    public function test_it_throws_an_exception_if_the_source_directory_is_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->renameDirectory('/../invalid', '/images');
    }

    public function test_it_throws_an_exception_if_the_target_directory_is_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->renameDirectory('/files', '/../images');
    }

    public function test_it_can_return_the_directories_stored_within_a_directory()
    {
        $adapter = $this->getAdapter();

        $directories = $adapter->listDirectories('/');

        $this->assertEquals([
            [
                'name'     => 'files',
                'pathname' => $this->getRootPath().'/files',
                'basename' => $this->getRootPath(),
                'created'  => date('Y-m-d H:i:s'),
            ]
        ], $directories);
    }

    public function test_it_throws_an_exception_if_the_directory_is_outside_the_root_when_listing_directories_in_a_directory()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->listDirectories('/../');
    }

    public function test_it_throws_an_exception_if_the_directory_doesnt_exist_when_listing_subdirectories()
    {
        $adapter = $this->getAdapter();

        $this->expectException(DirectoryDoesntExistException::class);

        $adapter->listDirectories('/missing');
    }

    public function test_it_can_get_a_directory()
    {
        $adapter = $this->getAdapter();

        $info = $adapter->getDirectory('/files');

        $this->assertEquals([
            'name'     => 'files',
            'pathname' => $this->getRootPath().'/files',
            'basename' => $this->getRootPath(),
            'created'  => date('Y-m-d H:i:s'),
        ], $info);
    }

    public function test_it_throws_an_exception_if_trying_to_get_a_directory_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->getDirectory('/../missing');
    }

    public function test_it_throws_an_exception_when_trying_to_get_a_directory_that_doesnt_exist()
    {
        $adapter = $this->getAdapter();

        $this->expectException(DirectoryDoesntExistException::class);

        $adapter->getDirectory('/missing');
    }

    public function test_it_can_check_if_a_file_exists()
    {
        $adapter = $this->getAdapter();

        $this->assertTrue($adapter->fileExists($this->getRootPath().'/files/test.txt'));
        $this->assertFalse($adapter->fileExists($this->getRootPath().'/files/missing.txt'));
    }

    public function test_it_can_create_a_file()
    {
        $adapter = $this->getAdapter();

        $info = $adapter->createFile('/files/write_test.txt', 'this is a test');

        $this->assertTrue($adapter->fileExists($this->getRootPath().'/files/write_test.txt'));

        $this->assertEquals([
            'name'     => 'write_test.txt',
            'pathname' => $this->getRootPath().'/files/write_test.txt',
            'basename' => $this->getRootPath().'/files',
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => 14,
        ], $info);
    }

    public function test_it_throws_an_exception_if_the_path_to_the_file_is_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->createFile('/../write_test.txt', 'this is a test');
    }

    public function test_it_overwrites_the_file_when_creating_a_file_with_a_path_that_already_exists()
    {
        $adapter = $this->getAdapter();

        $info = $adapter->createFile('/files/test.txt', 'this is a test');

        $this->assertEquals([
            'name'     => 'test.txt',
            'pathname' => $this->getRootPath().'/files/test.txt',
            'basename' => $this->getRootPath().'/files',
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => 14,
        ], $info);
    }

    public function test_it_can_delete_a_file()
    {
        $adapter = $this->getAdapter();

        $result = $adapter->deleteFile('/files/test.txt');

        $this->assertTrue($result);
        $this->assertFalse($adapter->fileExists($this->getRootPath().'/files/test.txt'));
    }

    public function test_it_returns_true_when_deleting_a_file_that_doesnt_exist()
    {
        $adapter = $this->getAdapter();

        $result = $adapter->deleteFile('/files/missing.txt');

        $this->assertTrue($result);
    }

    public function test_it_throws_an_exception_when_trying_to_delete_a_file_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->deleteFile('/../missing.txt');
    }

    public function test_it_can_rename_a_file()
    {
        $adapter = $this->getAdapter();

        $info = $adapter->renameFile('/files/test.txt', '/files/tests/renamed.txt');

        $this->assertEquals([
            'name'     => 'renamed.txt',
            'pathname' => $this->getRootPath().'/files/tests/renamed.txt',
            'basename' => $this->getRootPath().'/files/tests',
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => 9,
        ], $info);

        $this->assertTrue($adapter->fileExists($this->getRootPath().'/files/tests/renamed.txt'));
        $this->assertFalse($adapter->fileExists($this->getRootPath().'/files/test.txt'));
    }

    public function test_it_throws_an_exception_if_the_source_file_is_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->renameFile('/../test.txt', '/renamed.txt');
    }

    public function test_it_throws_an_exception_if_the_target_file_is_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->renameFile('/files/test.txt', '/../renamed.txt');
    }

    public function test_it_can_update_a_file()
    {
        $adapter = $this->getAdapter();

        $info = $adapter->updateFile('/files/test.txt', 'this is updated content');

        $this->assertEquals([
            'name'     => 'test.txt',
            'pathname' => $this->getRootPath().'/files/test.txt',
            'basename' => $this->getRootPath().'/files',
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => 23,
        ], $info);
    }

    public function test_it_throws_an_exception_when_updating_a_file_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->updateFile('/../missing.txt', '');
    }

    public function test_it_creates_the_file_when_updating_a_non_existing_file()
    {
        $adapter = $this->getAdapter();

        $info = $adapter->updateFile('/files/missing.txt', 'test file');

        $this->assertEquals([
            'name'     => 'missing.txt',
            'pathname' => $this->getRootPath().'/files/missing.txt',
            'basename' => $this->getRootPath().'/files',
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => 9,
        ], $info);

        $this->assertTrue($adapter->fileExists($this->getRootPath().'/files/missing.txt'));
    }

    public function test_it_can_return_an_array_of_files_in_a_directory()
    {
        $adapter = $this->getAdapter();

        $files = $adapter->listFiles('/files');

        $this->assertInternalType('array', $files);
        $this->assertEquals([
            [
                'name'     => 'test.txt',
                'pathname' => $this->getRootPath().'/files/test.txt',
                'basename' => $this->getRootPath().'/files',
                'created'  => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
                'size'     => 9,
            ]
        ], $files);
    }

    public function test_it_throws_an_exception_if_the_directory_is_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->listFiles('/../missing');
    }

    public function test_it_throws_an_exception_if_the_directory_doesnt_exist_when_listing_files()
    {
        $adapter = $this->getAdapter();

        $this->expectException(DirectoryDoesntExistException::class);

        $adapter->listFiles('/missing');
    }

    public function test_it_can_read_the_content_of_a_file()
    {
        $adapter = $this->getAdapter();

        $content = $adapter->readFile('/files/test.txt');

        $this->assertEquals('test file', $content);
    }

    public function test_it_throws_an_exception_when_trying_to_read_a_file_which_doesnt_exist()
    {
        $adapter = $this->getAdapter();

        $this->expectException(FileDoesntExistException::class);

        $adapter->readFile('/files/missing.txt');
    }

    public function test_it_throws_an_exception_when_trying_to_read_a_file_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->readFile('/../missing.txt');
    }

    public function test_it_can_return_info_about_a_given_file()
    {
        $adapter = $this->getAdapter();

        $info = $adapter->getFile('/files/test.txt');

        $this->assertEquals([
            'name'     => 'test.txt',
            'pathname' => $this->getRootPath().'/files/test.txt',
            'basename' => $this->getRootPath().'/files',
            'created'  => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'size'     => 9,
        ], $info);
    }

    public function test_it_throws_an_exception_if_the_file_doesnt_exist_when_retrieving_it()
    {
        $adapter = $this->getAdapter();

        $this->expectException(FileDoesntExistException::class);

        $adapter->getFile('/missing.txt');
    }

    public function test_it_throws_an_exception_when_trying_to_get_a_file_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->getFile('/../missing.txt');
    }

    public function test_it_can_return_the_size_of_a_directory()
    {
        $adapter = $this->getAdapter();

        $size = $adapter->getDirectorySize('/');

        $this->assertEquals(9, $size);
    }

    public function test_it_throws_an_exception_when_trying_to_get_the_size_of_a_directory_outside_the_root()
    {
        $adapter = $this->getAdapter();

        $this->expectException(PathNotInRootException::class);

        $adapter->getDirectorySize('/../');
    }

    public function test_it_throws_an_exception_when_trying_to_get_the_size_of_a_directory_that_doesnt_exist()
    {
        $adapter = $this->getAdapter();

        $this->expectException(DirectoryDoesntExistException::class);

        $adapter->getDirectorySize('/missing');
    }

    /**
     * @return string
     */
    abstract protected function getRootPath();

    /**
     * @return AdapterInterface
     */
    abstract protected function getAdapter(): AdapterInterface;
}
