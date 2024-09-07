<?php

declare(strict_types=1);

namespace App\Domain\Service\Discount\DiscountRules;

use App\Domain\ValueObject\Discount\DiscountResult;
use App\Domain\ValueObject\Order\Order;

interface DiscountRule
{
    public function calculate(Order $order): DiscountResult;
}
