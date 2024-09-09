<?php

declare(strict_types=1);

namespace App\Domain\Service\Discount;

use App\Application\DTO\OrderRequestDTO;
use App\Domain\Entity\Product\CartItemCollection;
use App\Domain\Service\Discount\DiscountRules\DiscountRule;
use App\Domain\ValueObject\CartItem\CartItem;
use App\Domain\ValueObject\Discount\DiscountResult;
use App\Domain\ValueObject\Order\Order;
use App\Domain\ValueObject\Price\InvalidPrice;
use App\Domain\ValueObject\Price\Price;

final class DiscountCalculator implements DiscountCalculatorService
{
    /** @var DiscountRule[] $discountRules */
    private array $discountRules;

    public function __construct(DiscountRule ...$discountRules)
    {
        $this->discountRules = $discountRules;
    }

    public function execute(OrderRequestDTO $orderRequest): array
    {
        $order = $this->getOrder($orderRequest);
        $totalPrice = $originalPrice = $this->getTotalPriceFromOrder($order);
        $rulesDiscounts = [];

        foreach ($this->discountRules as $discountRule) {
            $discountResult = $discountRule->calculate($order);
            $rulesDiscounts[] = $this->calculateTotalPrice(
                $discountResult,
                $totalPrice,
                $originalPrice,
            );
        }

        return  [
            'originalPrice' => $originalPrice->getPrice(),
            'totalPrice' => $totalPrice->getPrice(),
            'discounts' => $rulesDiscounts
        ];
    }

    private function getTotalPriceFromOrder(Order $order): Price
    {
        $totalPrice = 0;
        foreach ($order->getCartItems() as $cartItem) {
            $totalPrice = $totalPrice + $cartItem->getTotalPrice()->getPrice();
        }

        return new Price($totalPrice);
    }

    private function getOrder(OrderRequestDTO $orderRequest): Order
    {
        $items = \array_map(
            fn(array $item) => new CartItem(
                productId: $item['product-id'],
                quantity: \intval($item['quantity']),
                unitPrice: new Price(\floatval($item['unit-price'])),
                totalPrice: new Price(\floatval($item['total'])),
            ),
            $orderRequest->getItems()
        );
        $cartItems = new CartItemCollection(...$items);

        return new Order(
            customerId: $orderRequest->getCustomerId(),
            cartItems: $cartItems
        );
    }

    /**
     * @throws InvalidPrice
     */
    private function calculateTotalPrice(
        DiscountResult $discountResult,
        Price &$totalPrice,
        Price $originalPrice
    ): array {
        $ruleDiscounts = [];

        if ($discountResult->getDiscountApplied()->getPrice() > 0) {
            try {
                $totalPrice = $totalPrice->subtract($discountResult->getDiscountApplied()->getPrice());
                $discountedAmount = $discountResult->getDiscountApplied()->getPrice();
            } catch (InvalidPrice $e) {
                $discountedAmount = $originalPrice->getPrice();
                $totalPrice = new Price(0);
            } finally {
                $ruleDiscounts = [
                    'discountedPrice' => $discountedAmount,
                    'reason' => $discountResult->getReason(),
                ];
            }
        }

        return $ruleDiscounts;
    }


}
