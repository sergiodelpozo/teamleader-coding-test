<?php

declare(strict_types=1);

namespace App\Domain\Entity\Category;

final class Category
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $code,
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

    public function getCode(): string
    {
        return $this->code;
    }
}
