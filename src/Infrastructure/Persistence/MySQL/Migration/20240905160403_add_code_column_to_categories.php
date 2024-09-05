<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCodeColumnToCategories extends AbstractMigration
{
    public function change(): void
    {
        $categories = $this->table('categories');
        $categories->addColumn('code', 'string', ['length' => 255])->save();
    }
}
