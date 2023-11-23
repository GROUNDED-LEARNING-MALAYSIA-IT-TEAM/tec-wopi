<?php
use Migrations\AbstractMigration;

class AddColumnUserInfoToFile extends AbstractMigration
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
        //column user info has a maximum size of 1024 ASCII characters.
        $table->addColumn('user_info', 'string', [
            'default' => null,
            'limit' => 1024,
            'null' => true,
            'after' => 'version'
        ]);
        $table->update();
    }

    public function down()
    {
        $table = $this->table('wopi_files');
        $table->removeColumn('user_info');
        $table->update();
    }
}
