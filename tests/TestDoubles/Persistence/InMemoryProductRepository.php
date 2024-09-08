<?php

declare(strict_types=1);

namespace App\Tests\TestDoubles\Persistence;

use App\Domain\Entity\Product\Product;
use App\Domain\Entity\Product\ProductCollection;
use App\Domain\Service\Repository\ProductRepository;

class InMemoryProductRepository implements ProductRepository
{
    private array $products = [];

    public function findById(string $id): ?Product
    {
        return $this->products[$id] ?? null;
    }

    public function saveProduct(Product $product): void
    {
        $this->products[$product->getId()] = $product;
    }

    public function findByCategoryCode(string $categoryCode): ProductCollection
    {
        $result = [];

        foreach ($this->products as $product) {
            if ($product->getCategory()->getCode() === $categoryCode) {
                $result[] = $product;
            }
        }

        return new ProductCollection(...$result);
    }
}
