<?php
use Migrations\AbstractMigration;

class AddColumnParentIdToWopiFiles extends AbstractMigration
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
        $table->addColumn('parent_id', 'integer', [
            'default' => null,
            'null' => true,
            'after' => 'id',
        ]);

        $table->addColumn('soft_delete', 'boolean', [
            'default' => false,
            'null' => false,
            'after' => 'user_info',
        ]);

        $table->addColumn('soft_delete_at', 'datetime', [
            'default' => null,
            'null' => true,
            'after' => 'soft_delete',
        ]);

        $table->addForeignKey(
            'parent_id', 'wopi_files', 'id', [
            'delete' => 'CASCADE',
            'update' => 'CASCADE',
        ]);





        $table->update();


    }
}
