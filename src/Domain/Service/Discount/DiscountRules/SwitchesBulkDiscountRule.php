<?php

declare(strict_types=1);

namespace App\Domain\Service\Discount\DiscountRules;

use App\Domain\Service\Discount\DiscountRules\DiscountRule;
use App\Domain\Service\Repository\ProductRepository;
use App\Domain\ValueObject\CartItem\CartItem;
use App\Domain\ValueObject\Discount\DiscountResult;
use App\Domain\ValueObject\Order\Order;
use App\Domain\ValueObject\Price\Price;

class SwitchesBulkDiscountRule implements DiscountRule
{
    private const REASON = 'Buy 5 switches, get 1 free';
    public const string SWITCH_CATEGORY_CODE = 'switch';
    public const int FREE_ITEM_THRESHOLD = 6;

    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function calculate(Order $order): DiscountResult
    {
        $discountToApply = 0;
        $switchProducts = $this->productRepository->findByCategoryCode(self::SWITCH_CATEGORY_CODE);

        /** @var CartItem $cartItem */
        foreach ($order->getCartItems() as $cartItem) {
            if ($switchProducts->containsId($cartItem->getProductId())) {
                $quantity = $cartItem->getQuantity();
                $freeProducts = \intdiv($quantity, self::FREE_ITEM_THRESHOLD);
                $discountToApply = $discountToApply + $cartItem->getUnitPrice()->multiply($freeProducts)->getPrice();
            }
        }


        return new DiscountResult(
            discountApplied: new Price($discountToApply),
            reason: $discountToApply > 0 ? self::REASON : ''
        );
    }
}
