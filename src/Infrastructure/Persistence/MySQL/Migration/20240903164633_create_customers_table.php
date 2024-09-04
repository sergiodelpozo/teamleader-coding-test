<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCustomersTable extends AbstractMigration
{
    public function change(): void
    {
        $products = $this->table('customers');
        $products->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('revenue', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addColumn('register_date', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
