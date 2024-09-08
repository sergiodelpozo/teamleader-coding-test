<?php

declare(strict_types=1);

namespace App\Domain\Entity\Product;

use App\Domain\Service\Collection\Collection;

class ProductCollection extends Collection
{
    /**
     * @inheritDoc
     */
    protected function getType(): string
    {
        return Product::class;
    }

    public function containsId(string $id): bool
    {
        foreach ($this->elements as $element) {
            if ($element->getId() === $id) {
                return true;
            }
        }

        return false;
    }
}
