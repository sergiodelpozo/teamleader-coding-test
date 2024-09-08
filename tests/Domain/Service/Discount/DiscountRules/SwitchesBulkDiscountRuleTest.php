<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service\Discount\DiscountRules;

use App\Domain\Entity\Product\CartItemCollection;
use App\Domain\Entity\Product\Product;
use App\Domain\Service\Discount\DiscountRules\SwitchesBulkDiscountRule;
use App\Domain\ValueObject\CartItem\CartItem;
use App\Domain\ValueObject\Order\Order;
use App\Domain\ValueObject\Price\Price;
use App\Tests\ObjectMother\CategoryObjectMother;
use App\Tests\ObjectMother\ProductObjectMother;
use App\Tests\TestDoubles\Persistence\InMemoryProductRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SwitchesBulkDiscountRuleTest extends TestCase
{
    private SwitchesBulkDiscountRule $sut;
    private Product $firstProduct;
    private Product $secondProduct;
    private InMemoryProductRepository $productRepository;

    public function setUp(): void
    {
        $this->firstProduct = ProductObjectMother::withParameters([
            'category' => CategoryObjectMother::withParameters(['code' => 'switch'])
        ]);
        $this->secondProduct = ProductObjectMother::withParameters([
            'category' => CategoryObjectMother::withParameters(['code' => 'switch'])
        ]);
        $this->productRepository = new InMemoryProductRepository();
        $this->productRepository->saveProduct($this->firstProduct);
        $this->productRepository->saveProduct($this->secondProduct);
        $this->sut = new SwitchesBulkDiscountRule($this->productRepository);
    }

    #[Test]
    public function calculateWithAnOrderThatHasMoreThanFiveItemsFromSwitchedCategoryAppliesTheDiscount(): void
    {
        $items = [
            new CartItem($this->firstProduct->getId(), 2, new Price(50), new Price(100)),
            new CartItem($this->secondProduct->getId(), 6, new Price(100), new Price(600)),
        ];

        $order = new Order(
            customerId: 5,
            cartItems: new CartItemCollection(
              ...$items
            )
        );
        $discounts = $this->sut->calculate($order);

        $this->assertEquals(100.0, $discounts->getDiscountApplied()->getPrice());
    }

    #[Test]
    public function calculateWithAnOrderThatHasTwoProductsWithFiveOrMoreItemsFromSwitchedCategoryAppliesTheDiscount(): void
    {
        $items = [
            new CartItem($this->firstProduct->getId(), 6, new Price(50), new Price(100)),
            new CartItem($this->secondProduct->getId(), 15, new Price(100), new Price(600)),
        ];

        $order = new Order(
            customerId: 5,
            cartItems: new CartItemCollection(
                ...$items
            )
        );
        $discounts = $this->sut->calculate($order);

        $this->assertEquals(250.0, $discounts->getDiscountApplied()->getPrice());
    }

    #[Test]
    public function calculateWithAnOrderThatHasLessThanSixItemsFromSwitchedCategoryReturnsEmptyDiscount(): void
    {
        $items = [
            new CartItem($this->firstProduct->getId(), 2, new Price(50), new Price(100)),
            new CartItem($this->secondProduct->getId(), 4, new Price(100), new Price(600)),
        ];

        $order = new Order(
            customerId: 5,
            cartItems: new CartItemCollection(
                ...$items
            )
        );
        $discounts = $this->sut->calculate($order);

        $this->assertEquals(0.0, $discounts->getDiscountApplied()->getPrice());
    }

    #[Test]
    public function calculateWithAnOrderWithProductsFromOtherCategoriesReturnsAnEmptyDiscount(): void
    {
        $product = ProductObjectMother::withParameters([
            'category' => CategoryObjectMother::withParameters(['code' => 'test'])
        ]);
        $this->productRepository->saveProduct($product);

        $items = [
            new CartItem($product->getId(), 6, new Price(100), new Price(600)),
        ];
        $order = new Order(
            customerId: 5,
            cartItems: new CartItemCollection(
                ...$items
            )
        );

        $discounts = $this->sut->calculate($order);

        $this->assertEquals(0.0, $discounts->getDiscountApplied()->getPrice());
    }
}
