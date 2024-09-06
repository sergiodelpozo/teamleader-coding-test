<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Persistence\MySQL\Repository;

use App\Domain\Entity\Customer\Customer;
use App\Domain\Entity\Customer\CustomerNotFound;
use App\Infrastructure\Persistence\MySQL\Repository\MysqlCustomerRepository;
use App\Tests\IntegrationTest;
use App\Tests\ObjectMother\CustomerObjectMother;
use PHPUnit\Framework\Attributes\Test;

class MysqlCustomerRepositoryTest extends IntegrationTest
{
    private readonly MysqlCustomerRepository $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new MysqlCustomerRepository($this->connection);
    }

    #[Test]
    public function findByIdGivenACustomerThatDontExistsThrowsCustomerNotFoundException(): void
    {
        $this->expectException(CustomerNotFound::class);

        $this->sut->findById(5);
    }

    #[Test]
    public function findByIdHavingAValidCustomerIdReturnsTheCustomer(): void
    {
        $customer = CustomerObjectMother::random();
        $this->createCustomer($customer);

        $result = $this->sut->findById($customer->getId());

        $this->assertEquals($customer, $result);
    }

    private function createCustomer(Customer $customer): void
    {
        $sql = 'INSERT INTO customers (id, name, revenue, register_date) VALUES (:id, :name, :revenue, :register_date)';

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':id', $customer->getId());
        $stmt->bindValue(':name', $customer->getName());
        $stmt->bindValue(':revenue', $customer->getRevenue()->getPrice());
        $stmt->bindValue(':register_date', $customer->getRegisterDate()->format('Y-m-d H:i:s'));

        $stmt->execute();
    }
}
