<?php
use Migrations\AbstractMigration;

class AlterLocksTable extends AbstractMigration
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
        $table = $this->table('locks');

        $table->addColumn('locked','boolean',[
            'default' => false,
            'null' => false,
            'after' => 'file_id',
        ]);
        $table->update();
    }
}
