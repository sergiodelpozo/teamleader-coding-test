<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MySQL\Repository;

use App\Domain\Entity\Discount\Discount;
use App\Domain\Service\Repository\DiscountRepository;
use App\Domain\ValueObject\Price\Price;

final class MysqlDiscountRepository implements DiscountRepository
{
    public function __construct(private readonly \PDO $connection)
    {
    }

    public function searchByOrderId(int $orderId): ?Discount
    {
        $sql = 'SELECT * 
                FROM discounts  
                WHERE order_id = :order_id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);
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

    public function save(Discount $discount): void
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
}
