<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Discount;

use App\Application\Command\CalculateDiscountCommand;
use App\Application\DTO\OrderRequestDTO;
use App\Application\Handlers\CalculateDiscountHandler;
use App\Domain\Entity\Customer\Exception\CustomerNotFound;
use App\Domain\Entity\Discount\Exception\DuplicatedDiscountOrder;
use App\Domain\Entity\Product\Exception\InvalidQuantity;
use App\Domain\Entity\Product\Exception\ProductNotFound;
use App\Domain\Exception\DomainException;
use App\Domain\Exception\EmptyOrderException;
use App\Domain\Exception\GeneralExceptionCodes;
use App\Domain\ValueObject\Price\InvalidPrice;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class CalculateDiscountController
{
    public function __construct(private readonly CalculateDiscountHandler $discountHandler)
    {
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $data = \json_decode($request->getBody()->getContents(), true);

            $command = new CalculateDiscountCommand(
                new OrderRequestDTO(
                    id: \intval($data['id']),
                    customerId: \intval($data['customer-id']),
                    items: $data['items'],
                )
            );
            $discountResults = $this->discountHandler->handle($command);

            $httpStatus = StatusCodeInterface::STATUS_OK;
            $responseData = [
                'code' => GeneralExceptionCodes::STATUS_OK,
                'message' => 'OK',
                'data' => $discountResults,
            ];
        } catch (DomainException $e) {
            $httpStatus = StatusCodeInterface::STATUS_BAD_REQUEST;
            $responseData = [
                'code' => $e->getInternalCode(),
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            ];
        } catch (\JsonException $e) {
            $httpStatus = StatusCodeInterface::STATUS_BAD_REQUEST;
            $responseData = [
                'code' => GeneralExceptionCodes::INVALID_REQUEST->value,
                'message' => 'Request body is invalid',
                'data' => [],
            ];
        }

        $response->getBody()->write(json_encode($responseData, JSON_THROW_ON_ERROR));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($httpStatus);
    }
}
