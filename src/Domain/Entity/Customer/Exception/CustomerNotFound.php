<?php

declare(strict_types=1);

namespace App\Domain\Entity\Customer\Exception;

use App\Domain\Entity\Customer\Customer;
use App\Domain\Exception\CustomerExceptionCodes;
use App\Domain\Exception\DomainException;

class CustomerNotFound extends DomainException
{
    const string CUSTOMER_NOT_FOUND = 'Customer with id "%d" not found.';

    public static function withId(int $id): self
    {
        return new self(
            internalCode: CustomerExceptionCodes::CUSTOMER_NOT_FOUND->value,
            message: \sprintf(self::CUSTOMER_NOT_FOUND, $id),
            data: ['id' => $id],
        );
    }
}
