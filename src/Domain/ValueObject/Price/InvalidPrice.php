<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\Price;

use App\Domain\Exception\DomainException;
use App\Domain\Exception\PriceExceptionCodes;

final class InvalidPrice extends DomainException
{
    private const PRICE_CANNOT_BE_LESS_THAN_ZERO = 'Invalid price %f, price cannot be less than zero.';
    private const PRICE_MULTIPLIER_CANNOT_BE_NEGATIVE = 'Invalid price multiplier %f, value cannot be negative.';

    public static function fromNegativePrice(float $price): self
    {
        return new self(
            internalCode: PriceExceptionCodes::NEGATIVE_PRICE->value,
            message: \sprintf(self::PRICE_CANNOT_BE_LESS_THAN_ZERO, $price),
            data: ['price' => $price],
        );
    }

    public static function withInvalidMultiplier(float $multiplier)
    {
        return new self(
            internalCode: PriceExceptionCodes::NEGATIVE_PRICE->value,
            message: \sprintf(self::PRICE_MULTIPLIER_CANNOT_BE_NEGATIVE, $multiplier),
            data: ['multiplier' => $multiplier],
        );
    }
}
