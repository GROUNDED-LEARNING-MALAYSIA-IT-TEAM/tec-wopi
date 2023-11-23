<?php
namespace EaglenavigatorSystem\Wopi\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use EaglenavigatorSystem\Wopi\Exception\FileManagementException;


/**
 * FileManagement component
 * Handles various file operations using core PHP functions.
 */
class FileManagementComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Creates a new file at the specified path with the given name.
     *
     * @param string $path The path where the file should be created.
     * @param string $name The name of the file to be created.
     * @return bool True on success, false on failure.
     */
    public function createFile($path, $name)
    {
        $filePath = $path . DIRECTORY_SEPARATOR . $name;
        try {
            $handle = fopen($filePath, 'w');
            if ($handle === false) {
                return false;
            }
            fclose($handle);
            return true;
        } catch (FileManagementException $e) {
            return false;
        }
    }

    /**
     * Deletes the file at the specified path.
     *
     * @param string $path The path of the file to be deleted.
     * @return bool True on success, false on failure.
     */
    public function deleteFile($path)
    {
        try {
            return unlink($path);
        } catch (FileManagementException $e) {
            return false;
        }
    }

    /**
     * Renames a file from the old path to the new path.
     *
     * @param string $oldPath The current path of the file.
     * @param string $newPath The new path for the file.
     * @return bool True on success, false on failure.
     */
    public function renameFile($oldPath, $newPath)
    {
        try {
            return rename($oldPath, $newPath);
        } catch (FileManagementException $e) {
            return false;
        }
    }

    /**
     * Copies a file from the source path to the destination path.
     *
     * @param string $sourcePath The path of the source file.
     * @param string $destinationPath The path where the file should be copied.
     * @return bool True on success, false on failure.
     */
    public function copyFile($sourcePath, $destinationPath)
    {
        try {
            return copy($sourcePath, $destinationPath);
        } catch (FileManagementException $e) {
            return false;
        }
    }

    /**
     * Moves a file from the source path to the destination path.
     *
     * @param string $sourcePath The current path of the file.
     * @param string $destinationPath The new path for the file.
     * @return bool True on success, false on failure.
     */
    public function moveFile($sourcePath, $destinationPath)
    {
        try {
            return rename($sourcePath, $destinationPath);
        } catch (FileManagementException $e) {
            return false;
        }
    }

    /**
     * Reads the content of a file at the specified path.
     *
     * @param string $path The path of the file.
     * @return string|bool The file content on success, false on failure.
     */
    public function readFile($path)
    {
        try {
            return file_get_contents($path);
        } catch (FileManagementException $e) {
            return false;
        }
    }

    /**
     * Writes the given content to the file at the specified path.
     *
     * @param string $path The path of the file.
     * @param string $content The content to write to the file.
     * @return bool True on success, false on failure.
     */
    public function writeFile($path, $content)
    {
        try {
            return file_put_contents($path, $content) !== false;
        } catch (FileManagementException $e) {
            return false;
        }
    }

    /**
     * Appends the given content to the file at the specified path.
     *
     * @param string $path The path of the file.
     * @param string $content The content to append to the file.
     * @return bool True on success, false on failure.
     */
    public function appendToFile($path, $content)
    {
        try {
            return file_put_contents($path, $content, FILE_APPEND) !== false;
        } catch (FileManagementException $e) {
            return false;
        }
    }

    /**
     * Returns the size of the file at the specified path.
     *
     * @param string $path The path of the file.
     * @return int|bool The file size on success, false on failure.
     */
    public function getFileSize($path)
    {
        try {
            return filesize($path);
        } catch (FileManagementException $e) {
            return false;
        }
    }

    /**
     * Returns the creation time of the file at the specified path.
     * Note: PHP does not have a native way to get the creation time. This function returns the last modification time.
     *
     * @param string $path The path of the file.
     * @return int|bool The creation time as a Unix timestamp on success, false on failure.
     */
    public function getFileCreationTime($path)
    {
        try {
            return filemtime($path);
        } catch (FileManagementException $e) {
            return false;
        }
    }

    /**
     * Returns the last modification time of the file at the specified path.
     *
     * @param string $path The path of the file.
     * @return int|bool The last modification time as a Unix timestamp on success, false on failure.
     */
    public function getFileModificationTime($path)
    {
        try {
            return filemtime($path);
        } catch (FileManagementException $e) {
            return false;
        }
    }
}
