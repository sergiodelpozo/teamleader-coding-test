<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Persistence\MySQL\Repository;

use App\Domain\Entity\Customer\Customer;
use App\Domain\ValueObject\Price\Price;
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
    public function findByIdGivenACustomerThatDontExistsReturnsNullValue(): void
    {
        $customer = $this->sut->findById(5);

        $this->assertNull($customer);
    }

    #[Test]
    public function findByIdHavingAValidCustomerIdReturnsTheCustomer(): void
    {
        $customer = CustomerObjectMother::random();
        $this->createCustomer($customer);

        $result = $this->sut->findById($customer->getId());

        $this->assertEquals($customer, $result);
    }

    #[Test]
    public function saveGivenANewCustomerPersistsTheCustomer(): void
    {
        $customer = CustomerObjectMother::withParameters([
            'id' => null,
            'name' => 'test'
        ]);

        $this->sut->save($customer);

        $persistedCustomer = $this->findByCustomerData($customer);

        $this->assertInstanceOf(Customer::class, $persistedCustomer);
    }

    #[Test]
    public function saveGivenAnExistingCustomerUpdatesTheCustomer(): void
    {
        $customer = CustomerObjectMother::withParameters([
            'revenue' => new Price(100)
        ]);
        $this->createCustomer($customer);
        $customer->changeRevenue(new Price(500));

        $this->sut->save($customer);

        $persistedCustomer = $this->sut->findById($customer->getId());

        $this->assertEquals(500.0, $persistedCustomer->getRevenue()->getPrice());
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

    private function findByCustomerData(Customer $customer): ?Customer
    {
        $sql = 'SELECT * 
                FROM customers 
                WHERE name = :name AND register_date = :register_date';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'name' => $customer->getName(),
            'register_date' => $customer->getRegisterDate()->format('Y-m-d H:i:s')
        ]);
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
}
