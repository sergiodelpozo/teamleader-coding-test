<?php

declare(strict_types=1);

namespace App\Domain\Exception;

enum GeneralExceptionCodes : string
{
    private const BASE_CODE = '999';

    case EMPTY_PRODUCT_LIST = self::BASE_CODE . '001';
}
