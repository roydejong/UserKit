<?php

use Phinx\Migration\AbstractMigration;

class CreateVisitorsTable extends AbstractMigration
{
    public function change()
    {
        $this->table('userkit_visitors')
            ->addColumn('fingerprint', 'string', ['length' => 40])
            ->addColumn('date', 'date')
            ->addColumn('page_views', 'integer', ['default' => 0])
            ->addColumn('agent_browser', 'string', ['length' => 32])
            ->addColumn('agent_platform', 'string', ['length' => 32])
            ->addColumn('referer', 'string', ['length' => 255, 'null' => true])
            ->addColumn('remote_address', 'string', ['length' => 48])
            ->addIndex('fingerprint')
            ->addIndex(['fingerprint', 'date'], ['unique' => true])
            ->create();
    }
}
