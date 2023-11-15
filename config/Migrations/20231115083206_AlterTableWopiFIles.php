<?php

use Migrations\AbstractMigration;

class AlterTableWopiFIles extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('wopi_files');

        $table->addColumn('file_name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
            'after' => 'file_uuid',
        ]);

        $table->addColumn('file_size', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
            'after' => 'file_name',
        ]);



        $table->update();
    }
}
