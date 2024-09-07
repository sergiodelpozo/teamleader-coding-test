<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Persistence\MySQL\Repository;

use App\Domain\Entity\Category\Category;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\Product\ProductNotFound;
use App\Infrastructure\Persistence\MySQL\Repository\MysqlProductRepository;
use App\Tests\IntegrationTest;
use App\Tests\ObjectMother\ProductObjectMother;
use PHPUnit\Framework\Attributes\Test;

class MysqlProductRepositoryTest extends IntegrationTest
{
    #[Test]
    public function findByIdGivenAValidProductIdReturnTheProduct(): void
    {
        $product = ProductObjectMother::random();
        $this->createCategory($product->getCategory());
        $this->createProduct($product);

        $sut = new MysqlProductRepository($this->connection);

        $result = $sut->findById($product->getId());

        $this->assertEquals($product, $result);
    }

    #[Test]
    public function findByIdGivenAProductIdThatNotExistsThrowsProductNotFoundException(): void
    {
        $product = ProductObjectMother::random();
        $sut = new MysqlProductRepository($this->connection);

        $this->expectException(ProductNotFound::class);

        $sut->findById($product->getId());
    }

    private function createProduct(Product $product): void
    {
        $sql = 'INSERT INTO products (id, category_id, unit_price) VALUES (:id, :category_id, :unit_price)';

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':id', $product->getId());
        $stmt->bindValue(':category_id', $product->getCategory()->getId());

        $stmt->execute();
    }

    private function createCategory(Category $category): void
    {
        $sql = 'INSERT INTO categories (id, code, name) VALUES (:id, :code, :name)';

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':id', $category->getId());
        $stmt->bindValue(':code', $category->getCode());
        $stmt->bindValue(':name', $category->getName());

        $stmt->execute();
    }
}
