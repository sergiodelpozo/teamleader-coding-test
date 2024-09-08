<?php

declare(strict_types=1);

namespace App\Domain\Service\Discount\DiscountRules;

use App\Domain\Service\Repository\CustomerRepository;
use App\Domain\ValueObject\CartItem\CartItem;
use App\Domain\ValueObject\Discount\DiscountResult;
use App\Domain\ValueObject\Order\Order;
use App\Domain\ValueObject\Price\Price;

class LoyaltyDiscountRule implements DiscountRule
{
    public const string REASON = 'The customer has already bought for over € 1000, ' .
    'gets a discount of 10% on the whole order.';
    const int REVENUE_THRESHOLD = 1000;
    const float PERCENTAGE_TO_DISCOUNT = 0.1;

    public function __construct(private readonly CustomerRepository $customerRepository)
    {
    }

    public function calculate(Order $order): DiscountResult
    {
        $customer = $this->customerRepository->findById($order->getCustomerId());
        $customerRevenue = $customer?->getRevenue()->getPrice() ?? 0;

        $discountResult = new DiscountResult(
            discountApplied: new Price(0),
            reason: '',
        );

        if ($customerRevenue >= self::REVENUE_THRESHOLD) {
            $discountResult = new DiscountResult(
                discountApplied: $this->getTotalOrderAmount($order)->multiply(self::PERCENTAGE_TO_DISCOUNT),
                reason: self::REASON,
            );
        }

        return $discountResult;
    }

    private function getTotalOrderAmount(Order $order): Price
    {
        $totalPrice = new Price(0);

        /** @var CartItem $cartItem */
        foreach ($order->getCartItems() as $cartItem) {
            $totalPrice = new Price($totalPrice->getPrice() + $cartItem->getTotalPrice()->getPrice());
        }

        return $totalPrice;
    }
}
