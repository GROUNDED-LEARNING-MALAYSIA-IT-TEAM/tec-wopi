<?php

namespace EaglenavigatorSystem\Wopi\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * WopiFilesFixture
 */
class WopiFilesFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'file_uuid' => ['type' => 'string', 'length' => 36, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'file_name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'file_size' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'file size', 'autoIncrement' => false, 'precision' => null],
        'file_extension' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'user_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'file_data' => ['type' => 'binary', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'file_path' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'version' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'version', 'precision' => null, 'fixed' => null],

        'created_at' => ['type' => 'timestamp', 'length' => null, 'null' => false, 'default' => 'current_timestamp()', 'comment' => '', 'precision' => null],
        'updated_at' => ['type' => 'timestamp', 'length' => null, 'null' => false, 'default' => 'current_timestamp()', 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        //define('TEST_FILE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'test_files' . DS);
        //TEST_FILE_PATH is one level up from the current directory
        //so that it can be used in the test cases
        define('TEST_FILE_PATH', dirname(dirname(__FILE__)) . DS . 'test_files' . DS);

        //read file content convert to blob
        $fileContent = file_get_contents(TEST_FILE_PATH . 'test.txt');

        $fileContentD = 'Therefore, WOPI locks must:

            Be associated with a single file.

            Contain a lock ID of maximum length 1024 ASCII characters.

            Prevent all changes to that file unless a proper lock ID is provided.

            Expire after 30 minutes unless refreshed. For more information, see RefreshLock.

            Not be associated with a particular user.';

        $target = TEST_FILE_PATH . 'test_d.txt';

        //if file target do not exist, create it
        //if file target exist, overwrite it
        file_put_contents($target, $fileContentD);
        $this->records = [
            [
                'id' => 1,
                'file_uuid' => '12345678-1234-1234-1234-0d0d0d0e3xcv',
                'file_name' => 'test_d.txt',
                'file_size' => filesize(TEST_FILE_PATH . 'test_d.txt'),
                'file_extension' => 'txt',
                'user_id' => 495,
                'file_data' => file_get_contents(TEST_FILE_PATH . 'test.txt'),
                'file_path' => TEST_FILE_PATH . 'test_d.txt',
                'version' => 1,
                'created_at' => 1699632818,
                'updated_at' => 1699632818,
            ],

            [
                'id' => 2,
                'file_uuid' => '12345678-1234-1234-1234-123456789012',
                'file_name' => 'test.txt',
                'file_size' => filesize(TEST_FILE_PATH . 'test.txt'),
                'file_extension' => 'txt',
                'user_id' => 495,
                'file_data' =>  $fileContent,
                'file_path' => TEST_FILE_PATH . 'test.txt',
                'version' => 1,
                'created_at' => 1699632818,
                'updated_at' => 1699632818,
            ],
        ];
        parent::init();
    }
}
