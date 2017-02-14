<?php

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    public function change()
    {
        $this->table('userkit_users')
            ->addColumn('key', 'string', ['length' => 12])
            ->addColumn('name', 'string', ['length' => 64, 'null' => true])
            ->addColumn('email', 'string', ['length' => 255, 'null' => true])
            ->addColumn('dt_first_seen', 'datetime')
            ->addColumn('dt_last_seen', 'datetime')
            ->addIndex('key', ['unique' => true])
            ->create();
    }
}
