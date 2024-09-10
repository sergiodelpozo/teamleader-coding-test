<?php

declare(strict_types=1);

namespace App\Domain\Exception;

enum ProductExceptionCodes : string
{
    private const string BASE_CODE = '004';

    case PRODUCT_NOT_FOUND = self::BASE_CODE . '000';
    case INVALID_QUANTITY = self::BASE_CODE . '001';
}
