<?php

declare(strict_types=1);

namespace App\Domain\Entity\Product;

use App\Domain\Service\Collection\Collection;
use App\Domain\ValueObject\CartItem\CartItem;

class CartItemCollection extends Collection
{
    /**
     * @inheritDoc
     */
    protected function getType(): string
    {
        return CartItem::class;
    }
}
