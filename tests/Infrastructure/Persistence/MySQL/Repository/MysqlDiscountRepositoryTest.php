<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Persistence\MySQL\Repository;

use App\Domain\Entity\Discount\Discount;
use App\Domain\ValueObject\Price\Price;
use App\Infrastructure\Persistence\MySQL\Repository\MysqlDiscountRepository;
use App\Tests\IntegrationTest;
use App\Tests\ObjectMother\DiscountObjectMother;
use PHPUnit\Framework\Attributes\Test;

final class MysqlDiscountRepositoryTest extends IntegrationTest
{
    #[Test]
    public function searchByOrderIdGivenAnOrderWithoutDiscountsReturnsNullValue(): void
    {
        $sut = new MysqlDiscountRepository($this->connection);

        $result = $sut->searchByOrderId(5);

        $this->assertNull($result);
    }

    #[Test]
    public function searchByOrderIdGivenAnExistingDiscountForSpecifiedOrderReturnsTheDiscount(): void
    {
        $discount = DiscountObjectMother::random();
        $this->createDiscount($discount);
        $sut = new MysqlDiscountRepository($this->connection);

        $result = $sut->searchByOrderId($discount->getOrderId());

        $this->assertEquals($discount, $result);
    }

    #[Test]
    public function saveGivenADiscountPersistTheDiscount(): void
    {
        $discount = DiscountObjectMother::withParameters([
            'id' => null
        ]);
        $sut = new MysqlDiscountRepository($this->connection);

        $sut->save($discount);

        $result = $sut->searchByOrderId($discount->getOrderId());

        $this->assertEquals($discount->getOrderId(), $result->getOrderId());
    }

    private function createDiscount(Discount $discount): void
    {
        $sql = 'INSERT INTO discounts (id, order_id, total_amount, discount_applied, total_discounted_amount) 
                    VALUES (:id, :order_id, :total_amount, :discount_applied, :total_discounted_amount)';

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':id', $discount->getId());
        $stmt->bindValue(':order_id', $discount->getOrderId());
        $stmt->bindValue(':total_amount', $discount->getTotalAmount()->getPrice());
        $stmt->bindValue(':discount_applied', $discount->getDiscountApplied()->getPrice());
        $stmt->bindValue(':total_discounted_amount', $discount->getFinalPrice()->getPrice());

        $stmt->execute();
    }

    private function getDiscountById(int $id): ?Discount
    {
        $sql = 'SELECT * 
                FROM discounts 
                WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        if ($data === false) {
            return null;
        }

        return new Discount(
            id: $data['id'],
            orderId: $data['order_id'],
            totalAmount: new Price(\floatval($data['total_amount'])),
            discountApplied: new Price(\floatval($data['discount_applied'])),
            finalPrice: new Price(\floatval($data['total_discounted_amount'])),
        );
    }
}
