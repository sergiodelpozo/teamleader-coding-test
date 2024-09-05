<?php

declare(strict_types=1);

namespace App\Domain\Entity\Customer;

use App\Domain\ValueObject\Price\Price;

final class Customer
{
    public function __construct(
        private int $id,
        private string $name,
        private \DateTimeImmutable $registerDate,
        private Price $revenue,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRegisterDate(): \DateTimeImmutable
    {
        return $this->registerDate;
    }

    public function getRevenue(): Price
    {
        return $this->revenue;
    }
}
