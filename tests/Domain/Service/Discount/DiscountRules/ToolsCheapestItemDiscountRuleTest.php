<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service\Discount\DiscountRules;

use App\Domain\Entity\Product\CartItemCollection;
use App\Domain\Entity\Product\Product;
use App\Domain\Service\Discount\DiscountRules\ToolsCheapestItemDiscountRule;
use App\Domain\ValueObject\CartItem\CartItem;
use App\Domain\ValueObject\Order\Order;
use App\Domain\ValueObject\Price\Price;
use App\Tests\ObjectMother\CategoryObjectMother;
use App\Tests\ObjectMother\ProductObjectMother;
use App\Tests\TestDoubles\Persistence\InMemoryProductRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ToolsCheapestItemDiscountRuleTest extends TestCase
{
    private Product $toolProduct;
    private Product $secondToolProduct;
    private Product $notToolProduct;
    private ToolsCheapestItemDiscountRule $sut;

    public function setUp(): void
    {
        $this->toolProduct = ProductObjectMother::withParameters([
            'category' => CategoryObjectMother::withParameters(['code' => 'tools'])
        ]);
        $this->secondToolProduct = ProductObjectMother::withParameters([
            'category' => CategoryObjectMother::withParameters(['code' => 'tools'])
        ]);
        $this->notToolProduct = ProductObjectMother::withParameters([
            'category' => CategoryObjectMother::withParameters(['code' => 'test'])
        ]);
        $productRepository = new InMemoryProductRepository();
        $productRepository->saveProduct($this->toolProduct);
        $productRepository->saveProduct($this->secondToolProduct);
        $productRepository->saveProduct($this->notToolProduct);

        $this->sut = new ToolsCheapestItemDiscountRule($productRepository);
    }

    #[Test]
    public function calculateWithAnOrderThatContainsTwoOrMoreToolsProductsAppliesTheDiscountOnTheCheapest(): void
    {
        $order = new Order(
            customerId: 5,
            cartItems: new CartItemCollection(
                new CartItem($this->toolProduct->getId(), 2, new Price(50), new Price(100))
            ),
        );

        $discount = $this->sut->calculate($order);

        $this->assertEquals(10.0, $discount->getDiscountApplied()->getPrice());
    }

    #[Test]
    public function calculateWithAnOrderThatContainsTwoDifferentToolsProductsAppliesTheDiscountOnTheCheapest(): void
    {
        $order = new Order(
            customerId: 5,
            cartItems: new CartItemCollection(
                new CartItem($this->toolProduct->getId(), 2, new Price(50), new Price(100)),
                new CartItem($this->secondToolProduct->getId(), 3, new Price(20), new Price(60)),
            ),
        );

        $discount = $this->sut->calculate($order);

        $this->assertEquals(4.0, $discount->getDiscountApplied()->getPrice());
    }

    #[Test]
    public function calculateWithAnOrderThatContainsTwoDifferentToolsInQuantityAppliesTheDiscountOnTheCheapest(): void
    {
        $order = new Order(
            customerId: 5,
            cartItems: new CartItemCollection(
                new CartItem($this->toolProduct->getId(), 1, new Price(50), new Price(100)),
                new CartItem($this->secondToolProduct->getId(), 1, new Price(20), new Price(60)),
            ),
        );

        $discount = $this->sut->calculate($order);

        $this->assertEquals(4.0, $discount->getDiscountApplied()->getPrice());
    }

    #[Test]
    public function calculateWithAnOrderWithoutToolsProductsReturnsEmptyDiscounts(): void
    {
        $order = new Order(
            customerId: 5,
            cartItems: new CartItemCollection(
                new CartItem($this->notToolProduct->getId(), 6, new Price(50), new Price(100)),
            ),
        );

        $discount = $this->sut->calculate($order);

        $this->assertEquals(0.0, $discount->getDiscountApplied()->getPrice());
    }

    #[Test]
    public function calculateWithAnOrderWithOneSingleToolProductReturnsEmptyDiscounts(): void
    {
        $order = new Order(
            customerId: 5,
            cartItems: new CartItemCollection(
                new CartItem($this->toolProduct->getId(), 1, new Price(50), new Price(100)),
            ),
        );

        $discount = $this->sut->calculate($order);

        $this->assertEquals(0.0, $discount->getDiscountApplied()->getPrice());
    }
}
