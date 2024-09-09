<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\DTO\OrderRequestDTO;

final readonly class CalculateDiscountCommand
{
    public function __construct(private OrderRequestDTO $order)
    {
    }

    public function getOrder(): OrderRequestDTO
    {
        return $this->order;
    }
}
