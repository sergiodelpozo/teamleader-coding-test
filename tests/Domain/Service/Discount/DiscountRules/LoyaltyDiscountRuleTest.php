<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service\Discount\DiscountRules;

use App\Domain\Entity\Product\CartItemCollection;
use App\Domain\Service\Discount\DiscountRules\LoyaltyDiscountRule;
use App\Domain\ValueObject\CartItem\CartItem;
use App\Domain\ValueObject\Order\Order;
use App\Domain\ValueObject\Price\Price;
use App\Tests\ObjectMother\CustomerObjectMother;
use App\Tests\TestDoubles\Persistence\InMemoryCustomerRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LoyaltyDiscountRuleTest extends TestCase
{
    #[Test]
    public function calculateWithACustomerThatHasMoreThanOneThousandAlreadyBoughtReturnsATenPercentDiscount(): void
    {
        $customer = CustomerObjectMother::withParameters([
            'revenue' => new Price(1000)
        ]);

        $customerRepository = new InMemoryCustomerRepository();
        $customerRepository->save($customer);
        $sut = new LoyaltyDiscountRule($customerRepository);

        $order = new Order(
            customerId: $customer->getId(),
            cartItems: new CartItemCollection(
                new CartItem(
                    productId: 'T01',
                    quantity: 1,
                    unitPrice: new Price(100),
                    totalPrice: new Price(100)
                )
            )
        );

        $discountResult = $sut->calculate($order);

        $this->assertEquals(10.0, $discountResult->getDiscountApplied()->getPrice());
    }

    #[Test]
    public function calculateWithACustomerThatHasLessThanOneThousandAlreadyBoughtReturnsAnEmptyDiscount(): void
    {
        $customer = CustomerObjectMother::withParameters([
            'revenue' => new Price(500)
        ]);

        $customerRepository = new InMemoryCustomerRepository();
        $customerRepository->save($customer);
        $sut = new LoyaltyDiscountRule($customerRepository);

        $order = new Order(
            customerId: $customer->getId(),
            cartItems: new CartItemCollection(
                new CartItem(
                    productId: 'T01',
                    quantity: 1,
                    unitPrice: new Price(100),
                    totalPrice: new Price(100)
                )
            )
        );

        $discountResult = $sut->calculate($order);

        $this->assertEquals(0.0, $discountResult->getDiscountApplied()->getPrice());
    }

    #[Test]
    public function calculateWithANonExistingUserReturnsAndEmptyDiscount(): void
    {
        $customerRepository = new InMemoryCustomerRepository();
        $sut = new LoyaltyDiscountRule($customerRepository);

        $order = new Order(
            customerId: 5,
            cartItems: new CartItemCollection(
                new CartItem(
                    productId: 'T01',
                    quantity: 1,
                    unitPrice: new Price(100),
                    totalPrice: new Price(100)
                )
            )
        );

        $discountResult = $sut->calculate($order);

        $this->assertEquals(0.0, $discountResult->getDiscountApplied()->getPrice());
    }
}
