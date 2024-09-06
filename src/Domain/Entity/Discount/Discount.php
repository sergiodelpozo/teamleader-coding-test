<?php

declare(strict_types=1);

namespace App\Domain\Entity\Discount;

use App\Domain\ValueObject\Price\Price;

final class Discount
{
    public function __construct(
        private ?int $id,
        private int $orderId,
        private Price $totalAmount,
        private Price $discountApplied,
        private Price $finalPrice,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getTotalAmount(): Price
    {
        return $this->totalAmount;
    }

    public function getDiscountApplied(): Price
    {
        return $this->discountApplied;
    }

    public function getFinalPrice(): Price
    {
        return $this->finalPrice;
    }
}
