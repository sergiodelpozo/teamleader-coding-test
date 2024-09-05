<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class ProductSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [
            'CategorySeeder',
        ];
    }

    public function run(): void
    {
        $data = [
            [
                'id' => 'A101',
                'category_id' => 1,
                'unit_price' => 19.50
            ],
            [
                'id' => 'A102',
                'category_id' => 2,
                'unit_price' => 49.50
            ],
            [
                'id' => 'A103',
                'category_id' => 2,
                'unit_price' => 20.50
            ],
            [
                'id' => 'A104',
                'category_id' => 1,
                'unit_price' => 15
            ]
        ];

        $this->table('products')->insert($data)->save();
    }
}
