<?php

declare(strict_types=1);

namespace App\Domain\Exception;

enum ProductExceptionCodes : string
{
    private const string BASE_CODE = '001';

    case PRODUCT_NOT_FOUND = self::BASE_CODE . '000';
}
