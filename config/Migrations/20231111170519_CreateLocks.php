<?php
use Migrations\AbstractMigration;

class CreateLocks extends AbstractMigration
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
        $table->addColumn('file_id', 'integer')
        ->addColumn('lock_id', 'string', ['limit' => 1024])
        ->addColumn('locked_by_user_id', 'integer')
        ->addColumn('expiration_time', 'datetime')
        ->addColumn('created_at', 'datetime')
        ->addColumn('updated_at', 'datetime')
        ->addIndex(['file_id'], ['unique' => true])
        ->create();
    }
}
