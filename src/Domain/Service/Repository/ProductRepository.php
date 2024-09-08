<?php

declare(strict_types=1);

namespace App\Domain\Service\Repository;

use App\Domain\Entity\Category\Category;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\Product\ProductCollection;

interface ProductRepository
{
    public function findById(string $id): ?Product;

    public function findByCategoryCode(string $categoryCode): ProductCollection;
}
