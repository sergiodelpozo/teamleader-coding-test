<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class CustomerSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Coca Cola',
                'register_date' => '2014-06-28',
                'revenue' => 492.12,
            ],
            [
                'id' => 2,
                'name' => 'Teamleader',
                'register_date' => '2015-01-15',
                'revenue' => 1505.95,
            ],
            [
                'id' => 3,
                'name' => 'Jeroen De Wit',
                'register_date' => '2016-02-11',
                'revenue' => 0,
            ],
        ];

        $customers = $this->table('customers');
        $customers->insert($data)->save();
    }
}
