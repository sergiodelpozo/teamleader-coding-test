<?php

declare(strict_types=1);

namespace App\Tests\ObjectMother;

use App\Domain\Entity\Discount\Discount;
use App\Domain\ValueObject\Price\Price;
use Faker\Factory;

final class DiscountObjectMother
{
    public static function withParameters(array $parameters): Discount
    {
        $faker = Factory::create();
        $totalAmount = new Price($faker->randomFloat(2, 50, 50000));

        return new Discount(
            id: \array_key_exists('id', $parameters) ? $parameters['id'] : $faker->unique()->randomNumber(5),
            orderId: $parameters['orderId'] ?? $faker->unique()->randomNumber(5),
            totalAmount: $parameters['totalAmount'] ?? $totalAmount,
            discountApplied: $parameters['discountApplied']
                ?? new Price($faker->randomFloat(2, 0, $totalAmount->getPrice())),
            finalPrice: $parameters['finalPrice']
                ?? new Price($faker->randomFloat(2, 0, $totalAmount->getPrice())),
        );
    }

    public static function random(): Discount
    {
        return self::withParameters([]);
    }
}
