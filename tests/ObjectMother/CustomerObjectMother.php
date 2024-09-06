<?php

declare(strict_types=1);

namespace App\Tests\ObjectMother;

use App\Domain\Entity\Customer\Customer;
use App\Domain\ValueObject\Price\Price;
use Faker\Factory;

final class CustomerObjectMother
{
    public static function withParameters(array $parameters): Customer
    {
        $faker = Factory::create();

        return new Customer(
            id: $parameters['id'] ?? $faker->unique()->randomNumber(3),
            name: $parameters['name'] ?? $faker->name(),
            registerDate: $parameters['registerDate'] ?? new \DateTimeImmutable($faker->date()),
            revenue: $parameters['revenue'] ?? new Price($faker->randomFloat(2, 0, 5000000)),
        );
    }

    public static function random(): Customer
    {
        return self::withParameters([]);
    }
}
