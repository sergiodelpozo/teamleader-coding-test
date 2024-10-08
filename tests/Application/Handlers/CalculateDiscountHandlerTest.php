<?php

declare(strict_types=1);

namespace App\Tests\Application\Handlers;

use App\Application\Command\CalculateDiscountCommand;
use App\Application\DTO\OrderRequestDTO;
use App\Application\Handlers\CalculateDiscountHandler;
use App\Domain\Entity\Customer\Customer;
use App\Domain\Entity\Customer\Exception\CustomerNotFound;
use App\Domain\Entity\Discount\Discount;
use App\Domain\Entity\Discount\Exception\DuplicatedDiscountOrder;
use App\Domain\Entity\Product\Exception\InvalidQuantity;
use App\Domain\Entity\Product\Exception\ProductNotFound;
use App\Domain\ValueObject\Price\Price;
use App\Tests\ObjectMother\CustomerObjectMother;
use App\Tests\ObjectMother\DiscountObjectMother;
use App\Tests\ObjectMother\ProductObjectMother;
use App\Tests\TestDoubles\Persistence\InMemoryCustomerRepository;
use App\Tests\TestDoubles\Persistence\InMemoryDiscountRepository;
use App\Tests\TestDoubles\Persistence\InMemoryProductRepository;
use App\Tests\TestDoubles\Services\DiscountCalculatorSpy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CalculateDiscountHandlerTest extends TestCase
{
    private InMemoryProductRepository $productRepository;
    private InMemoryDiscountRepository $discountRepository;
    private InMemoryCustomerRepository $customerRepository;
    private DiscountCalculatorSpy $discountCalculatorSpy;
    private CalculateDiscountHandler $sut;
    private Customer $customer;

    public function setUp(): void
    {
        $this->productRepository = new InMemoryProductRepository();
        $this->discountCalculatorSpy = new DiscountCalculatorSpy();
        $this->discountRepository = new InMemoryDiscountRepository();
        $this->customerRepository = new InMemoryCustomerRepository();
        $this->customer = CustomerObjectMother::withParameters([
            'id' => 5,
            'revenue' => new Price(100.0)
        ]);
        $this->customerRepository->save($this->customer);
        $this->sut = new CalculateDiscountHandler(
            $this->productRepository,
            $this->discountRepository,
            $this->customerRepository,
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
    public function handleHavingValidInputDataUpdateTheRevenueOfTheCustomer(): void
    {
        $product = ProductObjectMother::random();
        $orderId = 150;
        $orderRequest = new OrderRequestDTO(
            id: $orderId,
            customerId: $this->customer->getId(),
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

        $customer = $this->customerRepository->findById($this->customer->getId());

        $this->assertEquals(350.0, $customer->getRevenue()->getPrice());
    }

     #[Test]
     public function handleHavingAValidOrderFromAnUnknownCustomerThrowsCustomerNotFoundException(): void
     {
         $orderRequest = new OrderRequestDTO(
             id: 150,
             customerId: 50,
             items: [
                 [
                     'product-id' => 'T999',
                     'quantity' => '6',
                     'unit-price' => '50.0',
                     'total' => '300.0',
                 ]
             ]
         );
         $order = new CalculateDiscountCommand($orderRequest);

         $this->expectException(CustomerNotFound::class);

         $this->sut->handle($order);
     }

    #[Test]
    public function handleHavingADuplicatedOrderThrowsDuplicatedOrderException(): void
    {
        $product = ProductObjectMother::random();
        $orderId = 150;
        $orderRequest = new OrderRequestDTO(
            id: $orderId,
            customerId: $this->customer->getId(),
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
        $this->discountRepository->save(DiscountObjectMother::withParameters(['orderId' => $orderId]));

        $this->discountCalculatorSpy->willReturn([
            'originalPrice' => '300.0',
            'totalPrice' => '250.0',
            'discounts' => [],
        ]);

        $this->expectException(DuplicatedDiscountOrder::class);

        $this->sut->handle($order);
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
