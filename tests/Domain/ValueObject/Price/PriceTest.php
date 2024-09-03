<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject\Price;

use App\Domain\ValueObject\Price\Price;
use App\Domain\ValueObject\Price\InvalidPrice;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PriceTest extends TestCase
{
    #[Test]
    #[DataProvider('pricesDataProvider')]
    public function onlyValidPricesAreInstantiated(?float $priceValue, bool $valid): void
    {
        if (!$valid) {
            $this->expectException(InvalidPrice::class);
        }
        $price = new Price($priceValue);
        $this->assertInstanceOf(Price::class, $price);
    }

    #[Test]
    public function multiplyHavingInvalidMultiplierThrowsAnInvalidPriceException(): void
    {
        $price = new Price(100);
        $expectedException = InvalidPrice::withInvalidMultiplier(-1);
        $thrownException =  null;

        try {
            $price->multiply(-1);
        } catch (InvalidPrice $exception) {
            $thrownException = $exception;
        }

        $this->assertEquals($expectedException, $thrownException);
    }

    #[Test]
    #[DataProvider('multiplierDataProvider')]
    public function multiplyHavingValidMultiplierReturnsExpectedPrice(float $price, float $multiplier, float $expected): void
    {
        $price = new Price($price);
        $newPrice = $price->multiply($multiplier);

        $this->assertEquals($expected, $newPrice->getPrice());
    }

    public static function pricesDataProvider(): array
    {
        return [
            'With valid price' => [100.80, true],
            'With negative price' => [-50, false],
        ];
    }

    public static function multiplierDataProvider(): array
    {
        return [
            'With an integer value' => [100, 5, 500],
            'With a float value' => [100, 0.5, 50],
            'With a zero value' => [100, 0, 0],
            'With a decimal calculation' => [150.50, 1.5, 225.75],
        ];
    }
}
