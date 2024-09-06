<?php

declare(strict_types=1);

namespace App\Domain\Entity\Customer;

use App\Domain\Exception\CustomerExceptionCodes;
use App\Domain\Exception\DomainException;

final class CustomerNotFound extends DomainException
{
    const string CUSTOMER_NOT_FOUND = 'Customer with ID %d not found.';

    public static function withId(int $id): self
    {
        return new self(
            internalCode: CustomerExceptionCodes::CUSTOMER_NOT_FOUND->value,
            message: \sprintf(self::CUSTOMER_NOT_FOUND, $id),
            data: ['customerId' => $id]
        );
    }
}
