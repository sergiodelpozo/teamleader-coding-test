<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MySQL\Repository;

use App\Domain\Entity\Customer\Customer;
use App\Domain\Service\Repository\CustomerRepository;
use App\Domain\ValueObject\Price\Price;

final class MysqlCustomerRepository implements CustomerRepository
{
    public function __construct(private \PDO $connection)
    {
    }

    public function findById(int $id): ?Customer
    {
        $sql = 'SELECT * 
                FROM customers  
                WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data === false) {
            return null;
        }

        return new Customer(
            id: $data['id'],
            name: $data['name'],
            registerDate: new \DateTimeImmutable($data['register_date']),
            revenue: new Price(\floatval($data['revenue'])),
        );
    }

    public function save(Customer $customer): void
    {
        $sql = 'REPLACE INTO customers (id, name, register_date, revenue) VALUES (:id, :name, :register_date, :revenue)';
        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'id' => $customer->getId(),
            'name' => $customer->getName(),
            'register_date' => $customer->getRegisterDate()->format('Y-m-d H:i:s'),
            'revenue' => $customer->getRevenue()->getPrice()
        ]);
    }
}
