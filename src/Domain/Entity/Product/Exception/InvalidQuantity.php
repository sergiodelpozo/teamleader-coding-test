<?php

declare(strict_types=1);

namespace App\Domain\Entity\Product\Exception;

use App\Domain\Exception\DomainException;
use App\Domain\Exception\ProductExceptionCodes;

final class InvalidQuantity extends DomainException
{
    public static function withProductId(string $productId): self
    {
        return new self(
            internalCode: ProductExceptionCodes::INVALID_QUANTITY->value,
            message: \sprintf('Quantity value for product %s is not valid.', $productId),
            data: ['productId' => $productId],
        );
    }
}
