<?php

declare(strict_types=1);

namespace App\Tests\TestDoubles\Services\Discount;

use App\Domain\Service\Discount\DiscountRules\DiscountRule;
use App\Domain\ValueObject\Discount\DiscountResult;
use App\Domain\ValueObject\Order\Order;

final class DiscountRuleStub implements DiscountRule
{
    private DiscountResult $discountResult;

    public function calculate(Order $order): DiscountResult
    {
        return $this->discountResult;
    }

    public function calculateWillReturn(DiscountResult $discountResult): void
    {
        $this->discountResult = $discountResult;
    }
}
