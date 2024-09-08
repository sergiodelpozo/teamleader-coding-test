<?php

declare(strict_types=1);

namespace App\Tests\TestDoubles\Persistence;

use App\Domain\Entity\Customer\Customer;
use App\Domain\Service\Repository\CustomerRepository;

class InMemoryCustomerRepository implements CustomerRepository
{
    /** @var Customer[]  */
    private array $customers = [];

    public function findById(int $id): ?Customer
    {
        if (!isset($this->customers[$id])) {
            return null;
        }

        return $this->customers[$id];
    }

    public function saveCustomer(Customer $customer): void
    {
        $this->customers[$customer->getId()] = $customer;
    }
}
