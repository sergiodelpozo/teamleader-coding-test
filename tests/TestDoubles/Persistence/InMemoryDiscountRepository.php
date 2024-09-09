<?php

declare(strict_types=1);

namespace App\Tests\TestDoubles\Persistence;

use App\Domain\Entity\Discount\Discount;
use App\Domain\Service\Repository\DiscountRepository;

class InMemoryDiscountRepository implements DiscountRepository
{
    private array $discounts = [];

    public function searchByOrderId(int $orderId): ?Discount
    {
        $result = null;

        foreach ($this->discounts as $discount) {
            if ($discount->getOrderId() === $orderId) {
                $result = $discount;
            }
        }

        return $result;
    }

    public function save(Discount $discount): void
    {
        $this->discounts[$discount->getId()] = $discount;
    }
}
