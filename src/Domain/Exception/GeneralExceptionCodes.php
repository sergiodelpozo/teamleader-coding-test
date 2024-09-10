<?php

declare(strict_types=1);

namespace App\Domain\Exception;

enum GeneralExceptionCodes : string
{
    private const BASE_ERROR_CODE = '999';
    public const STATUS_OK = '000000';

    case EMPTY_PRODUCT_LIST = self::BASE_ERROR_CODE . '000';
    case INVALID_REQUEST = self::BASE_ERROR_CODE . '001';
}
