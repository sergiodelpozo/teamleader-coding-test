<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Price\Price;

final class Product
{
    public function __construct(
        private string $id,
        private Price $unitPrice,
        private Category $category,
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
