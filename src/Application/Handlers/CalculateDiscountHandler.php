<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Command\CalculateDiscountCommand;
use App\Application\DTO\OrderRequestDTO;
use App\Domain\Entity\Customer\Customer;
use App\Domain\Entity\Customer\Exception\CustomerNotFound;
use App\Domain\Entity\Discount\Discount;
use App\Domain\Entity\Discount\Exception\DuplicatedDiscountOrder;
use App\Domain\Entity\Product\Exception\InvalidQuantity;
use App\Domain\Entity\Product\Exception\ProductNotFound;
use App\Domain\Service\Discount\DiscountCalculatorService;
use App\Domain\Service\Repository\CustomerRepository;
use App\Domain\Service\Repository\DiscountRepository;
use App\Domain\Service\Repository\ProductRepository;
use App\Domain\ValueObject\Price\InvalidPrice;
use App\Domain\ValueObject\Price\Price;

final class CalculateDiscountHandler
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly DiscountRepository $discountRepository,
        private readonly CustomerRepository $customerRepository,
        private readonly DiscountCalculatorService $discountCalculator,
    )
    {
    }

    /**
     * @throws CustomerNotFound
     * @throws DuplicatedDiscountOrder
     * @throws InvalidPrice
     * @throws InvalidQuantity
     * @throws ProductNotFound
     */
    public function handle(CalculateDiscountCommand $discountCommand): array
    {
        $order = $discountCommand->getOrder();
        $customer = $this->validateOrder($order);

        $discountResult = $this->discountCalculator->execute($order);

        $totalPrice = new Price(\floatval($discountResult['originalPrice']));
        $finalPrice = new Price(\floatval($discountResult['totalPrice']));

        $customer->changeRevenue($customer->getRevenue()->add($finalPrice->getPrice()));
        $this->customerRepository->save($customer);

        $this->discountRepository->save(
            new Discount(
                id: null,
                orderId: $order->getId(),
                totalAmount: $totalPrice,
                discountApplied: $totalPrice->subtract($finalPrice->getPrice()),
                finalPrice: $finalPrice,
            )
        );

        return [
            'id' => $order->getId(),
            'customerId' => $order->getCustomerId(),
            ...$discountResult
        ];
    }

    /**
     * @throws CustomerNotFound
     * @throws DuplicatedDiscountOrder
     * @throws InvalidQuantity
     * @throws ProductNotFound
     */
    private function validateOrder(OrderRequestDTO $order): Customer
    {
        $customer = $this->customerRepository->findById($order->getCustomerId());
        if ($customer === null) {
            throw CustomerNotFound::withId($order->getCustomerId());
        }
        $discountOrder = $this->discountRepository->searchByOrderId($order->getId());
        if ($discountOrder !== null) {
            throw DuplicatedDiscountOrder::withOrderId($order->getId());
        }

        $this->validateProductList($order);

        return $customer;
    }

    /**
     * @throws ProductNotFound
     * @throws InvalidQuantity
     */
    private function validateProductList(OrderRequestDTO $order): void
    {
        $items = $order->getItems();
        foreach ($items as $item) {
            if (!isset($item['product-id'])) {
                throw ProductNotFound::withoutId();
            }
            if (!isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
                throw InvalidQuantity::withProductId($item['product-id']);
            }

            $product = $this->productRepository->findById($item['product-id']);

            if ($product === null) {
                throw ProductNotFound::withId($item['product-id']);
            }
        }
    }
}
