<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class CategorySeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Tools'
            ],
            [
                'id' => 2,
                'name' => 'Switches'
            ],
            [
                'id' => 3,
                'name' => 'Other'
            ]
        ];

        $this->table('categories')->insert($data)->save();
    }
}
