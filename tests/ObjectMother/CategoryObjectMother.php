<?php

declare(strict_types=1);

namespace App\Tests\ObjectMother;

use App\Domain\Entity\Category\Category;
use Faker\Factory;

final class CategoryObjectMother
{
    public static function random(): Category
    {
        return self::withParameters([]);
    }

    public static function withParameters(array $parameters): Category
    {
        $faker = Factory::create();

        return new Category(
            id: $parameters['id'] ?? $faker->unique()->randomNumber(5),
            name: $parameters['name'] ?? $faker->word(),
            code: $faker->unique()->word
        );
    }
}
