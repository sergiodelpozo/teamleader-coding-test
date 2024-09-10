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
            ],
            [
                'id' => 'A102',
                'category_id' => 2,
            ],
            [
                'id' => 'A103',
                'category_id' => 2,
            ],
            [
                'id' => 'A104',
                'category_id' => 1,
            ]
        ];

        $this->table('products')->insert($data)->save();
    }
}
