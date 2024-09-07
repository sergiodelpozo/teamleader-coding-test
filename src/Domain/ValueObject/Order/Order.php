<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\Order;

use App\Domain\Entity\Product\CartItemCollection;

final readonly class Order
{
    public function __construct(
        private int $customerId,
        private CartItemCollection $cartItems,
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getCartItems(): CartItemCollection
    {
        return $this->cartItems;
    }
}
