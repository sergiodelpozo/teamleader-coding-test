<?php

namespace App\Domain\Exception;

enum CustomerExceptionCodes : string
{
    private const string BASE_CODE = '001';

    case CUSTOMER_NOT_FOUND = self::BASE_CODE . '000';
}
