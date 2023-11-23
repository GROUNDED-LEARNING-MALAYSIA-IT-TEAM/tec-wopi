<?php

namespace EaglenavigatorSystem\Wopi\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use EaglenavigatorSystem\Wopi\Controller\Component\FileManagementComponent;

class FileManagementComponentTest extends TestCase
{
    public $FileManagement;

    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->FileManagement = new FileManagementComponent($registry);
    }

    public function tearDown()
    {
        unset($this->FileManagement);

        parent::tearDown();
    }

    public function testCreateFile()
    {
        $path = sys_get_temp_dir();
        $name = 'testfile.txt';

        $result = $this->FileManagement->createFile($path, $name);
        $this->assertTrue($result);
        $this->assertFileExists($path . DIRECTORY_SEPARATOR . $name);

        // Clean up
        unlink($path . DIRECTORY_SEPARATOR . $name);
    }

    public function testDeleteFile()
    {
        $path = sys_get_temp_dir();
        $name = 'testfile.txt';

        // Create a file for testing
        touch($path . DIRECTORY_SEPARATOR . $name);

        $result = $this->FileManagement->deleteFile($path . DIRECTORY_SEPARATOR . $name);
        $this->assertTrue($result);
        $this->assertTrue(!file_exists($path . DIRECTORY_SEPARATOR . $name));
    }

    public function testRenameFile()
    {
        $path = sys_get_temp_dir();
        $oldName = 'oldfile.txt';
        $newName = 'newfile.txt';

        // Create a file for testing
        touch($path . DIRECTORY_SEPARATOR . $oldName);

        $result = $this->FileManagement->renameFile($path . DIRECTORY_SEPARATOR . $oldName, $path . DIRECTORY_SEPARATOR . $newName);
        $this->assertTrue($result);
        $this->assertFileExists($path . DIRECTORY_SEPARATOR . $newName);
        $this->assertFalse(file_exists($path . DIRECTORY_SEPARATOR . $oldName));
        // Clean up
        unlink($path . DIRECTORY_SEPARATOR . $newName);
    }
    // ...

    public function testCopyFile()
    {
        $path = sys_get_temp_dir();
        $sourceName = 'sourcefile.txt';
        $destinationName = 'destinationfile.txt';

        // Create a file for testing
        touch($path . DIRECTORY_SEPARATOR . $sourceName);

        $result = $this->FileManagement->copyFile($path . DIRECTORY_SEPARATOR . $sourceName, $path . DIRECTORY_SEPARATOR . $destinationName);
        $this->assertTrue($result);
        $this->assertFileExists($path . DIRECTORY_SEPARATOR . $destinationName);

        // Clean up
        unlink($path . DIRECTORY_SEPARATOR . $sourceName);
        unlink($path . DIRECTORY_SEPARATOR . $destinationName);
    }

    public function testMoveFile()
    {
        $path = sys_get_temp_dir();
        $sourceName = 'sourcefile.txt';
        $destinationName = 'movedfile.txt';

        // Create a file for testing
        touch($path . DIRECTORY_SEPARATOR . $sourceName);

        $result = $this->FileManagement->moveFile($path . DIRECTORY_SEPARATOR . $sourceName, $path . DIRECTORY_SEPARATOR . $destinationName);
        $this->assertTrue($result);
        $this->assertFileExists($path . DIRECTORY_SEPARATOR . $destinationName);
        $this->assertFalse(file_exists($path . DIRECTORY_SEPARATOR . $sourceName));

        // Clean up
        unlink($path . DIRECTORY_SEPARATOR . $destinationName);
    }

    public function testReadFile()
    {
        $path = sys_get_temp_dir();
        $name = 'testfile.txt';
        $content = 'Hello, world!';

        // Create a file for testing
        file_put_contents($path . DIRECTORY_SEPARATOR . $name, $content);

        $result = $this->FileManagement->readFile($path . DIRECTORY_SEPARATOR . $name);
        $this->assertEquals($content, $result);

        // Clean up
        unlink($path . DIRECTORY_SEPARATOR . $name);
    }

    public function testWriteFile()
    {
        $path = sys_get_temp_dir();
        $name = 'testfile.txt';
        $content = 'Hello, world!';

        $this->FileManagement->writeFile($path . DIRECTORY_SEPARATOR . $name, $content);
        $this->assertStringEqualsFile($path . DIRECTORY_SEPARATOR . $name, $content);

        // Clean up
        unlink($path . DIRECTORY_SEPARATOR . $name);
    }

    public function testAppendToFile()
    {
        $path = sys_get_temp_dir();
        $name = 'testfile.txt';
        $content1 = 'Hello';
        $content2 = ', world!';

        $this->FileManagement->writeFile($path . DIRECTORY_SEPARATOR . $name, $content1);
        $this->FileManagement->appendToFile($path . DIRECTORY_SEPARATOR . $name, $content2);
        $this->assertStringEqualsFile($path . DIRECTORY_SEPARATOR . $name, $content1 . $content2);

        // Clean up
        unlink($path . DIRECTORY_SEPARATOR . $name);
    }

    public function testGetFileSize()
    {
        $path = sys_get_temp_dir();
        $name = 'testfile.txt';
        $content = 'Hello, world!';

        file_put_contents($path . DIRECTORY_SEPARATOR . $name, $content);

        $result = $this->FileManagement->getFileSize($path . DIRECTORY_SEPARATOR . $name);
        $this->assertEquals(strlen($content), $result);

        // Clean up
        unlink($path . DIRECTORY_SEPARATOR . $name);
    }

    public function testGetFileCreationTime()
    {
        // This test might be limited by the capabilities of the file system
        $path = sys_get_temp_dir();
        $name = 'testfile.txt';

        touch($path . DIRECTORY_SEPARATOR . $name);
        $expectedTime = filemtime($path . DIRECTORY_SEPARATOR . $name);

        $result = $this->FileManagement->getFileCreationTime($path . DIRECTORY_SEPARATOR . $name);
        $this->assertEquals($expectedTime, $result);

        // Clean up
        unlink($path . DIRECTORY_SEPARATOR . $name);
    }

    public function testGetFileModificationTime()
    {
        $path = sys_get_temp_dir();
        $name = 'testfile.txt';

        touch($path . DIRECTORY_SEPARATOR . $name);
        $expectedTime = filemtime($path . DIRECTORY_SEPARATOR . $name);

        $result = $this->FileManagement->getFileModificationTime($path . DIRECTORY_SEPARATOR . $name);
        $this->assertEquals($expectedTime, $result);

        // Clean up
        unlink($path . DIRECTORY_SEPARATOR . $name);
    }

    // ... Rest of the test case code

    // TODO: Add similar tests for copyFile, moveFile, readFile, writeFile, appendToFile, getFileSize, getFileCreationTime, getFileModificationTime methods
}
