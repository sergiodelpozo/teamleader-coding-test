<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTables extends AbstractMigration
{
    public function change(): void
    {
        $categories = $this->table('categories');
        $categories->addColumn('name', 'string', ['limit' => 255])
            ->create();

        $products = $this->table('products', ['id' => false, 'primary_key' => 'id']);
        $products->addColumn('id', 'string', ['limit' => 36, 'null' => false])
                 ->addColumn('category_id', 'integer', ['signed' => false, 'null' => false])
                 ->addForeignKey('category_id', 'categories', 'id')
                 ->addColumn('unit_price', 'decimal', ['precision' => 10, 'scale' => 2])
                 ->create();
    }
}
