<?php

namespace App\Domain\Exception;

enum PriceExceptionCodes : string
{
    private const BASE_CODE = '000';

    case NEGATIVE_PRICE = self::BASE_CODE . '001';
}
