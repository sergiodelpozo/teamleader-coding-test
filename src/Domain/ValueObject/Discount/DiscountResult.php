<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\Discount;

use App\Domain\ValueObject\Price\Price;

final readonly class DiscountResult
{
    public function __construct(
        private Price $discountApplied,
        private string $reason,
    ) {
    }

    public function getDiscountApplied(): Price
    {
        return $this->discountApplied;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
