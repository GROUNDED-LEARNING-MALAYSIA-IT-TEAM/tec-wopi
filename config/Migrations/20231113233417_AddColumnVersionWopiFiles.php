<?php
use Migrations\AbstractMigration;

class AddColumnVersionWopiFiles extends AbstractMigration
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
        //add column version to wopi_files
        $table = $this->table('wopi_files');
        $table->addColumn('version', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
            'after' => 'file_uuid'
        ]);

        $table->update();
    }
}
