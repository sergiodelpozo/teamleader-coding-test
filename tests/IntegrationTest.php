<?php

declare(strict_types=1);

namespace App\Tests;

use App\Infrastructure\Persistence\MySQL\PDOFactory;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    protected \PDO $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = PDOFactory::create();

        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }

        unset($this->connection);

        parent::tearDown();
    }
}
