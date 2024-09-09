<?php

declare(strict_types=1);

namespace App\Domain\Entity\Product\Exception;

use App\Domain\Exception\DomainException;
use App\Domain\Exception\ProductExceptionCodes;

final class ProductNotFound extends DomainException
{
    private const string PRODUCT_NOT_FOUND = 'Product with id "%s" not found.';
    private const string PRODUCT_ID_CANNOT_BE_EMPTY = 'Product id cannot be empty.';

    public static function withId(string $id): self
    {
        return new self(
            internalCode: ProductExceptionCodes::PRODUCT_NOT_FOUND->value,
            message: \sprintf(self::PRODUCT_NOT_FOUND, $id),
            data: ['productId' => $id]
        );
    }

    public static function withoutId(): self
    {
        return new self(
            internalCode: ProductExceptionCodes::PRODUCT_NOT_FOUND->value,
            message: self::PRODUCT_ID_CANNOT_BE_EMPTY,
        );
    }
}
