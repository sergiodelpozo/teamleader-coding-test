<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Exception\EmptyOrderException;

final readonly class OrderRequestDTO
{
    private int $id;
    private int $customerId;
    private array $items;

    /**
     * @throws EmptyOrderException
     */
    public function __construct(
        int $id,
        int $customerId,
        array $items,
    ) {
        $this->validate($items);

        $this->id = $id;
        $this->customerId = $customerId;
        $this->items = $items;
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

    /**
     * @throws EmptyOrderException
     */
    private function validate(array $items): void
    {
        if (empty($items)) {
            throw EmptyOrderException::fromEmptyItemsList();
        }
    }
}
