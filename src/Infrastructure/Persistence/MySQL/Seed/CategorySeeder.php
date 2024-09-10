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
                'name' => 'Tools',
                'code' => 'tools'
            ],
            [
                'id' => 2,
                'name' => 'Switches',
                'code' => 'switch'
            ],
            [
                'id' => 3,
                'name' => 'Other',
                'code' => 'other'
            ]
        ];

        $this->table('categories')->insert($data)->save();
    }
}
