<?php
use Migrations\AbstractMigration;

class AddColumnVersionWopi extends AbstractMigration
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

        $table->addColumn('version', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
            'after' => 'file_path',
        ]);


        $table->update();
    }
}
