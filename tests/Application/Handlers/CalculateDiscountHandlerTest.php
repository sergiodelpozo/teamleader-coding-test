<?php

declare(strict_types=1);

namespace App\Tests\Application\Handlers;

use App\Application\Command\CalculateDiscountCommand;
use App\Application\DTO\OrderRequestDTO;
use App\Application\Handlers\CalculateDiscountHandler;
use App\Domain\Entity\Discount\Discount;
use App\Domain\Entity\Product\Exception\InvalidQuantity;
use App\Domain\Entity\Product\Exception\ProductNotFound;
use App\Tests\ObjectMother\ProductObjectMother;
use App\Tests\TestDoubles\Persistence\InMemoryDiscountRepository;
use App\Tests\TestDoubles\Persistence\InMemoryProductRepository;
use App\Tests\TestDoubles\Services\DiscountCalculatorSpy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CalculateDiscountHandlerTest extends TestCase
{
    private InMemoryProductRepository $productRepository;
    private InMemoryDiscountRepository $discountRepository;
    private DiscountCalculatorSpy $discountCalculatorSpy;
    private CalculateDiscountHandler $sut;

    public function setUp(): void
    {
        $this->productRepository = new InMemoryProductRepository();
        $this->discountCalculatorSpy = new DiscountCalculatorSpy();
        $this->discountRepository = new InMemoryDiscountRepository();
        $this->sut = new CalculateDiscountHandler(
            $this->productRepository,
            $this->discountRepository,
            $this->discountCalculatorSpy
        );
    }

    #[Test]
    public function handleHavingInvalidProductThrowsInvalidProductException(): void
    {
        $order = new CalculateDiscountCommand(new OrderRequestDTO(
            id: 1,
            customerId: 5,
            items: [
                'product-id' => '1'
            ]
        ));

        $this->expectException(ProductNotFound::class);

        $this->sut->handle($order);
    }

    #[Test]
     public function handleHavingInvalidQuantityThrowsInvalidDiscountException(): void
     {
         $product = ProductObjectMother::random();
         $order = new CalculateDiscountCommand(new OrderRequestDTO(
             id: 1,
             customerId: 5,
             items: [
                [
                    'product-id' => $product->getId(),
                    'quantity' => '-9',
                    'unit-price' => '500',
                    'total' => '0',
                ]
             ]
         ));
         $this->productRepository->saveProduct($product);

         $this->expectException(InvalidQuantity::class);

         $this->sut->handle($order);
     }

     #[Test]
     public function handleHavingValidInputDataCallsTheDiscountCalculatorService(): void
     {
         $product = ProductObjectMother::random();
         $orderRequest = new OrderRequestDTO(
             id: 1,
             customerId: 5,
             items: [
                 [
                     'product-id' => $product->getId(),
                     'quantity' => '6',
                     'unit-price' => '50.0',
                     'total' => '300.0',
                 ]
             ]
         );
         $this->discountCalculatorSpy->willReturn([
             'originalPrice' => '300.0',
             'totalPrice' => '250.0',
             'discounts' => [],
         ]);
         $order = new CalculateDiscountCommand($orderRequest);
         $this->productRepository->saveProduct($product);

         $this->sut->handle($order);

         $this->assertEquals($orderRequest, $this->discountCalculatorSpy->getLastArguments()[0]);
     }

    #[Test]
    public function handleHavingValidInputDataPersistTheDiscounts(): void
    {
        $product = ProductObjectMother::random();
        $orderId = 150;
        $orderRequest = new OrderRequestDTO(
            id: $orderId,
            customerId: 5,
            items: [
                [
                    'product-id' => $product->getId(),
                    'quantity' => '6',
                    'unit-price' => '50.0',
                    'total' => '300.0',
                ]
            ]
        );
        $order = new CalculateDiscountCommand($orderRequest);
        $this->productRepository->saveProduct($product);
        $this->discountCalculatorSpy->willReturn([
            'originalPrice' => '300.0',
            'totalPrice' => '250.0',
            'discounts' => [],
        ]);

        $this->sut->handle($order);

        $discount = $this->discountRepository->searchByOrderId($orderId);

        $this->assertInstanceOf(Discount::class, $discount, 'Discount not found in the repository');
        $this->assertEquals(300.0, $discount->getTotalAmount()->getPrice());
        $this->assertEquals(250.0, $discount->getFinalPrice()->getPrice());
        $this->assertEquals(50.0, $discount->getDiscountApplied()->getPrice());
    }

    #[Test]
    public function handleHavingValidInputDataReturnsExpectedDiscountsResult(): void
    {
        $product = ProductObjectMother::random();
        $orderId = 150;
        $orderRequest = new OrderRequestDTO(
            id: $orderId,
            customerId: 5,
            items: [
                [
                    'product-id' => $product->getId(),
                    'quantity' => '6',
                    'unit-price' => '50.0',
                    'total' => '300.0',
                ]
            ]
        );
        $order = new CalculateDiscountCommand($orderRequest);
        $this->productRepository->saveProduct($product);
        $discountResult = [
            'originalPrice' => '300.0',
            'totalPrice' => '250.0',
            'discounts' => [
                [
                    'discountedPrice' => '50.0',
                    'reason' => 'Test rule applied'
                ]
            ],
        ];
        $this->discountCalculatorSpy->willReturn($discountResult);
        $expected = [
            'id' => $order->getOrder()->getId(),
            'customerId' => $order->getOrder()->getCustomerId(),
            ...$discountResult
        ];

        $result = $this->sut->handle($order);

        $this->assertEquals($expected, $result);
    }
}
