<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropPriceColumnInProducts extends AbstractMigration
{
    public function change(): void
    {
        $this->table('products')->removeColumn('unit_price')->save();
    }
}
