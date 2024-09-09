<?php

declare(strict_types=1);

namespace App\Tests\Application\DTO;

use App\Application\DTO\OrderRequestDTO;
use App\Domain\Exception\EmptyOrderException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OrderRequestDTOTest extends TestCase
{
    #[Test]
    public function dtoNotHavingAnyItemsThrowsEmptyOrderException(): void
    {
        $this->expectException(EmptyOrderException::class);

        $sut = new OrderRequestDTO(
            id: 1,
            customerId: 5,
            items: []
        );
    }
}
