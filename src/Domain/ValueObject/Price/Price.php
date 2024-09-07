<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\Price;

final class Price
{
    private float $price;

    /**
     * @throws InvalidPrice
     */
    public function __construct(float $price)
    {
        $this->validate($price);
        $this->price = $price;
    }

    /**
     * @throws InvalidPrice
     */
    private function validate(float $price): void
    {
        if ($price < 0) {
            throw InvalidPrice::fromNegativePrice($price);
        }
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @throws InvalidPrice
     */
    public function multiply(float $multiplier): Price
    {
        if ($multiplier < 0) {
            throw InvalidPrice::withInvalidMultiplier($multiplier);
        }

        return new self($this->price * $multiplier);
    }

    /**
     * @throws InvalidPrice
     */
    public function subtract(float $number)
    {
        return new self($this->price - $number);
    }
}
