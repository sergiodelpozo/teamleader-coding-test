<?php

declare(strict_types=1);

namespace App\Domain\Entity\Discount\Exception;

use App\Domain\Exception\DiscountExceptionCodes;
use App\Domain\Exception\DomainException;

final class DuplicatedDiscountOrder extends DomainException
{
    private const string ORDER_ALREADY_HAS_A_DISCOUNT_APPLIED = 'Order with id "%d" already has a discount applied.';

    public static function withOrderId(int $orderId): self
    {
        return new self(
            internalCode: DiscountExceptionCodes::ORDER_ALREADY_HAVE_A_DISCOUNT->value,
            message: \sprintf(self::ORDER_ALREADY_HAS_A_DISCOUNT_APPLIED, $orderId),
            data: ['orderId' => $orderId],
        );
    }
}
