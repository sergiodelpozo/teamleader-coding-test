<?php

declare(strict_types=1);

namespace App\Domain\Service\Repository;

use App\Domain\Entity\Discount\Discount;

interface DiscountRepository
{
    public function searchByOrderId(int $orderId): ?Discount;
    public function save(Discount $discount): void;
}
