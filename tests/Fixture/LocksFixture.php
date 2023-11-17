<?php

namespace EaglenavigatorSystem\Wopi\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LocksFixture
 */
class LocksFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'file_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'lock_id' => ['type' => 'string', 'length' => 1024, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'locked_by_user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'expiration_time' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'created_at' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'updated_at' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'file_id' => ['type' => 'unique', 'columns' => ['file_id'], 'length' => []],
            'fk_wopi_file' => ['type' => 'foreign', 'columns' => ['file_id'], 'references' => ['wopi_files', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
        $this->records = [
            [
                'id' => 1,
                'file_id' => 1,
                'lock_id' => 'xxxxxxxxxxxxxxxxxxxxxxxxx',
                'locked_by_user_id' => 495,
                'expiration_time' => '2023-11-13 12:15:58',
                'created_at' => '2023-11-13 12:15:58',
                'updated_at' => '2023-11-13 12:15:58',
            ],
            [
                'id' => 2,
                'file_id' => 2,
                'lock_id' => 'xxxxxxxxxxxxxxx7777777',
                'locked_by_user_id' => 495,
                'expiration_time' => '2023-11-13 12:15:58',
                'created_at' => '2023-11-13 12:15:58',
                'updated_at' => '2023-11-13 12:15:58',
            ],
        ];
        parent::init();
    }
}
