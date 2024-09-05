<?php

declare(strict_types=1);

namespace App\Tests\ObjectMother;

use App\Domain\Entity\Product\Product;
use App\Domain\ValueObject\Price\Price;
use Faker\Factory;

final class ProductObjectMother
{
    public static function random(): Product
    {
        return self::withParameters([]);
    }

    public static function withParameters(array $parameters): Product
    {
        $faker = Factory::create();

        return new Product(
            id: $parameters['id'] ?? "A" . $faker->randomNumber(3),
            unitPrice: $parameters['unitPrice'] ?? new Price($faker->randomFloat(2, 0.50, 500)),
            category:  $parameters['category'] ?? CategoryObjectMother::random()
        );
    }
}
