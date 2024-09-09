<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Persistence\MySQL\Repository;

use App\Domain\Entity\Category\Category;
use App\Domain\Entity\Product\Exception\ProductNotFound;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\Product\ProductCollection;
use App\Infrastructure\Persistence\MySQL\Repository\MysqlProductRepository;
use App\Tests\IntegrationTest;
use App\Tests\ObjectMother\CategoryObjectMother;
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

    #[Test]
    public function findByCategoryCodeGivenValidCategoryCodeReturnsAListOfProductsOfTheCategory(): void
    {
        $category = CategoryObjectMother::withParameters(['code' => 'test']);
        $otherCategory = CategoryObjectMother::withParameters(['code' => 'other-category']);
        $this->createCategory($category);
        $this->createCategory($otherCategory);
        $product = ProductObjectMother::withParameters([
            'id' => 'T01',
            'category' => $category
        ]);
        $secondProduct = ProductObjectMother::withParameters([
            'id' => 'T02',
            'category' => $category
        ]);
        $thirdProduct = ProductObjectMother::withParameters([
            'id' => 'T03',
            'category' => $otherCategory
        ]);
        $this->createProduct($product);
        $this->createProduct($secondProduct);
        $this->createProduct($thirdProduct);

        $expected = new ProductCollection(
            $product,
            $secondProduct
        );

        $sut = new MysqlProductRepository($this->connection);

        $products = $sut->findByCategoryCode('test');

        $this->assertEquals($expected, $products);
    }

    private function createProduct(Product $product): void
    {
        $sql = 'INSERT INTO products (id, category_id) VALUES (:id, :category_id)';

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
