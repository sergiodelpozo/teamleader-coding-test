<?php

declare(strict_types=1);

namespace App\Domain\Service\Repository;

use App\Domain\Entity\Product\Product;

interface ProductRepository
{
    public function findById(string $id): ?Product;
}
