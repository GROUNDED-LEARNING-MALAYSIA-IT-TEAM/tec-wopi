<?php
use Migrations\AbstractMigration;

class UserToken extends AbstractMigration
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
        $table = $this->table('user_sessions');
        $table->addColumn('token', 'string', [
            'limit' => 255,
            'default' => null,
            'null' => true,
            'after' => 'user_id',
        ]);

        $table->addColumn('token_expires', 'datetime', [
            'default' => null,
            'null' => true,
            'after' => 'token',
        ]);

        $table->update();
    }
}
