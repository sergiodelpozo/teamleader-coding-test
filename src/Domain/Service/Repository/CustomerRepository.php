<?php

declare(strict_types=1);

namespace App\Domain\Service\Repository;

use App\Domain\Entity\Customer\Customer;

interface CustomerRepository
{
    public function findById(int $id): ?Customer;
}
