<?php

namespace App\Domain\Exception;

enum PriceExceptionCodes : string
{
    private const string BASE_CODE = '003';

    case NEGATIVE_PRICE = self::BASE_CODE . '000';
}
