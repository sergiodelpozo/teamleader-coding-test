<?php

declare(strict_types=1);

namespace App\Tests\TestDoubles\Services;

use App\Application\DTO\OrderRequestDTO;
use App\Domain\Service\Discount\DiscountCalculatorService;

class DiscountCalculatorSpy implements DiscountCalculatorService
{
    private array $lastArgumentsNotify = [];
    private array $return = [];

    public function execute(OrderRequestDTO $orderRequest): array
    {
        $this->lastArgumentsNotify = [$orderRequest];

        return $this->return;
    }

    public function getLastArguments(): array
    {
        return $this->lastArgumentsNotify;
    }

    public function willReturn(array $result): void
    {
        $this->return = $result;
    }
}
