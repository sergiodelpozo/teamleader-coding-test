<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class EmptyOrderException extends DomainException
{
    public static function fromEmptyItemsList(): self
    {
        return new self(
            internalCode: GeneralExceptionCodes::EMPTY_PRODUCT_LIST->value,
            message: 'Item list is empty.',
        );
    }
}
