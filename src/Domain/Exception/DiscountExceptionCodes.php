<?php

namespace App\Domain\Exception;

enum DiscountExceptionCodes : string
{
    private const string BASE_CODE = '003';

    case ORDER_ALREADY_HAVE_A_DISCOUNT = self::BASE_CODE . '000';
}
