<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service\Discount;

use App\Application\DTO\OrderRequestDTO;
use App\Domain\Service\Discount\DiscountCalculator;
use App\Domain\ValueObject\Discount\DiscountResult;
use App\Domain\ValueObject\Price\Price;
use App\Tests\TestDoubles\Services\Discount\DiscountRuleStub;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DiscountCalculatorTest extends TestCase
{
    #[Test]
    public function discountCalculatorWithoutAnyRuleReturnsAnEmptyArray(): void
    {
        $sut = new DiscountCalculator();

        $result = $sut->execute($this->getDefaultRequestDTO());

        $this->assertEmpty($result['discounts']);
    }

    #[Test]
    public function discountCalculatorCallAllTheSpecifiedDiscountRulesAndReturnsTheResults(): void
    {
        $firstRule = new DiscountRuleStub();
        $firstRule->calculateWillReturn(new DiscountResult(
            discountApplied: new Price(10.5),
            reason: 'First test rule applied'
        ));
        $secondRule = new DiscountRuleStub();
        $secondRule->calculateWillReturn(new DiscountResult(
            discountApplied: new Price(5.5),
            reason: 'Second test rule applied'
        ));
        $sut = new DiscountCalculator(
            $firstRule,
            $secondRule
        );
        $orderRequest = $this->getDefaultRequestDTO();
        $expected = [
            "originalPrice" => 30.0,
            "totalPrice" => 14.0,
            "discounts" => [
                [
                    'discountedPrice' => 10.5,
                    'reason' => 'First test rule applied'
                ],
                [
                    'discountedPrice' => 5.5,
                    'reason' => 'Second test rule applied'
                ]
            ]
        ];

        $result = $sut->execute($orderRequest);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function discountCalculatorWhenTheDiscountsExceededTheTotalAmountItSetsToZeroTheAmount(): void
    {
        $firstRule = new DiscountRuleStub();
        $firstRule->calculateWillReturn(new DiscountResult(
            discountApplied: new Price(35),
            reason: 'First test rule applied'
        ));
        $sut = new DiscountCalculator(
            $firstRule,
        );
        $orderRequest = $this->getDefaultRequestDTO();
        $expected = [
            "originalPrice" => 30.0,
            "totalPrice" => 0.0,
            "discounts" => [
                [
                    'discountedPrice' => 30,
                    'reason' => 'First test rule applied'
                ],
            ]
        ];

        $result = $sut->execute($orderRequest);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function discountCalculatorHavingTwoDifferentProductsApplyTheDiscountsToAllOfThem(): void
    {
        $firstRule = new DiscountRuleStub();
        $firstRule->calculateWillReturn(new DiscountResult(
            discountApplied: new Price(2),
            reason: 'First test rule applied'
        ));
        $sut = new DiscountCalculator(
            $firstRule,
        );
        $expected = [
            "originalPrice" => 55.0,
            "totalPrice" => 53.0,
            "discounts" => [
                [
                    'discountedPrice' => 2,
                    'reason' => 'First test rule applied'
                ],
            ]
        ];

        $order = new OrderRequestDTO(
            id: 3,
            customerId: 10,
            items: [
                [
                    "product-id" => "T02",
                    "quantity" => "3",
                    "unit-price" => "10",
                    "total" => "30"
                ],
                [
                    "product-id" => "T03",
                    "quantity" => "5",
                    "unit-price" => "5",
                    "total" => "25"
                ]
            ]
        );
        $result = $sut->execute($order);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function discountCalculatorWithTwoDifferentProductsWithTwoDifferentRulesApplyTheDiscountsToAllOfThem(): void
    {
        $firstRule = new DiscountRuleStub();
        $firstRule->calculateWillReturn(new DiscountResult(
            discountApplied: new Price(2),
            reason: 'First test rule applied'
        ));
        $secondRule = new DiscountRuleStub();
        $secondRule->calculateWillReturn(new DiscountResult(
            discountApplied: new Price(8),
            reason: 'Second test rule applied'
        ));
        $sut = new DiscountCalculator(
            $firstRule,
            $secondRule
        );
        $expected = [
            "originalPrice" => 55.0,
            "totalPrice" => 45.0,
            "discounts" => [
                [
                    'discountedPrice' => 2,
                    'reason' => 'First test rule applied'
                ],
                [
                    'discountedPrice' => 8,
                    'reason' => 'Second test rule applied'
                ],
            ]
        ];

        $order = new OrderRequestDTO(
            id: 3,
            customerId: 10,
            items: [
                [
                    "product-id" => "T02",
                    "quantity" => "3",
                    "unit-price" => "10",
                    "total" => "30"
                ],
                [
                    "product-id" => "T03",
                    "quantity" => "5",
                    "unit-price" => "5",
                    "total" => "25"
                ]
            ]
        );
        $result = $sut->execute($order);

        $this->assertEquals($expected, $result);
    }

    private function getDefaultRequestDTO(): OrderRequestDTO
    {
        return new OrderRequestDTO(
            id: 1,
            customerId: 5,
            items: [
                [
                    "product-id" => "T01",
                    "quantity" => "3",
                    "unit-price" => "10",
                    "total" => "30"
                ]
            ]
        );
    }
}
