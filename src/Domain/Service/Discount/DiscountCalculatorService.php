<?php

declare(strict_types=1);

namespace App\Domain\Service\Discount;

use App\Application\DTO\OrderRequestDTO;

interface DiscountCalculatorService
{
    public function execute(OrderRequestDTO $orderRequest): array;
}
