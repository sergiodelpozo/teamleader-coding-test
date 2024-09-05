<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateDiscountsTable extends AbstractMigration
{
    public function change(): void
    {
        $discounts = $this->table('discounts');
        $discounts->addColumn('order_id', 'integer', ['null' => false])
            ->addColumn('total_amount', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addColumn('discount_applied', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addColumn('total_discounted_amount', 'decimal', ['precision' => 10, 'scale' => 2])
            ->create();
    }
}
