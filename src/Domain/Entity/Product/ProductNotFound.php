<?php

declare(strict_types=1);

namespace App\Domain\Entity\Product;

use App\Domain\Exception\DomainException;
use App\Domain\Exception\ProductExceptionCodes;

final class ProductNotFound extends DomainException
{
    const string PRODUCT_NOT_FOUND = 'Product with id "%s" not found.';

    public static function withId(string $id): self
    {
        return new self(
            internalCode: ProductExceptionCodes::PRODUCT_NOT_FOUND->value,
            message: sprintf(self::PRODUCT_NOT_FOUND, $id),
            data: ['productId' => $id]
        );
    }
}
