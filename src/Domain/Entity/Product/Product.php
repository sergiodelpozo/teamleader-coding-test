<?php

declare(strict_types=1);

namespace App\Domain\Entity\Product;

use App\Domain\Entity\Category\Category;
use App\Domain\ValueObject\Price\Price;

final class Product
{
    public function __construct(
        private readonly string $id,
        private readonly Price $unitPrice,
        private readonly Category $category,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUnitPrice(): Price
    {
        return $this->unitPrice;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
