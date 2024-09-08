<?php

declare(strict_types=1);

namespace App\Domain\Service\Discount\DiscountRules;

use App\Domain\Entity\Product\CartItemCollection;
use App\Domain\Entity\Product\ProductCollection;
use App\Domain\Service\Repository\ProductRepository;
use App\Domain\ValueObject\CartItem\CartItem;
use App\Domain\ValueObject\Discount\DiscountResult;
use App\Domain\ValueObject\Order\Order;
use App\Domain\ValueObject\Price\Price;

class ToolsCheapestItemDiscountRule implements DiscountRule
{
    private const string REASON = '20% of discount on the cheapest product of category tools.';
    public const float MULTIPLIER_DISCOUNT_PERCENTAGE = 0.2;
    public const int THRESHOLD_ITEMS_TO_APPLY_DISCOUNTS = 2;

    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function calculate(Order $order): DiscountResult
    {
        $tools = $this->productRepository->findByCategoryCode('tools');

        $toolsItemsInCart = $this->getToolsItemsInCart($order, $tools);
        $numberOfToolsItems = $this->getNumberOfToolsInCart($toolsItemsInCart);

        $cheapestProduct = null;
        if ($numberOfToolsItems >= self::THRESHOLD_ITEMS_TO_APPLY_DISCOUNTS) {
            $cheapestProduct = $this->getCheapestToolProduct($toolsItemsInCart);
        }

        if ($cheapestProduct === null) {
            return new DiscountResult(
                discountApplied: new Price(0),
                reason: ''
            );
        }

        $discount = $cheapestProduct->getUnitPrice()->multiply(self::MULTIPLIER_DISCOUNT_PERCENTAGE);

        return new DiscountResult(
            discountApplied: $discount,
            reason: self::REASON
        );
    }

    private function getCheapestToolProduct(CartItemCollection $toolsInCart): ?CartItem
    {
        $cheapestProduct = null;

        /** @var CartItem $cartItem */
        foreach ($toolsInCart as $cartItem) {
            if ($cheapestProduct === null) {
                $cheapestProduct = $cartItem;
            }
            if ($cheapestProduct->getUnitPrice()->getPrice() > $cartItem->getUnitPrice()->getPrice()) {
                $cheapestProduct = $cartItem;
            }
        }

        return $cheapestProduct;
    }

    private function getToolsItemsInCart(Order $order, ProductCollection $tools): CartItemCollection
    {
        $toolsItemsInCart = new CartItemCollection();

        foreach ($order->getCartItems() as $cartItem) {
            if ($tools->containsId($cartItem->getProductId())) {
                $toolsItemsInCart->add($cartItem);
            }
        }

        return $toolsItemsInCart;
    }

    private function getNumberOfToolsInCart(CartItemCollection $toolsItemsInCart)
    {
        $quantity = 0;

        /** @var CartItem $item */
        foreach ($toolsItemsInCart as $item) {
            $quantity += $item->getQuantity();
        }

        return $quantity;
    }
}
