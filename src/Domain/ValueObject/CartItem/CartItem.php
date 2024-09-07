<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\CartItem;

use App\Domain\ValueObject\Price\Price;

final readonly class CartItem
{
    public function __construct(
        private string $productId,
        private int $quantity,
        private Price $unitPrice,
        private Price $totalPrice,
    ) {
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): Price
    {
        return $this->unitPrice;
    }

    public function getTotalPrice(): Price
    {
        return $this->totalPrice;
    }
}
