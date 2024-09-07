<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class OrderRequestDTO
{
    public function __construct(
        private int $id,
        private int $customerId,
        private array $items,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
