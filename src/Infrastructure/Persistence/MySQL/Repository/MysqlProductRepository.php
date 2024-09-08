<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MySQL\Repository;

use App\Domain\Entity\Category\Category;
use App\Domain\Entity\Product\Product;
use App\Domain\Entity\Product\ProductCollection;
use App\Domain\Entity\Product\ProductNotFound;
use App\Domain\Service\Repository\ProductRepository;
use App\Domain\ValueObject\Price\Price;
use PDO;

final class MysqlProductRepository implements ProductRepository
{
    public function __construct(private readonly PDO $connection)
    {
    }

    /**
     * @throws ProductNotFound
     */
    public function findById(string $id): ?Product
    {
        $sql = 'SELECT p.*, c.code as category_code, c.name as category_name 
                FROM products as p 
                    INNER JOIN categories as c on c.id = p.category_id 
                WHERE p.id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data === false) {
            throw ProductNotFound::withId($id);
        }

        $category = new Category(
            id: $data['category_id'],
            name: $data['category_name'],
            code: $data['category_code'],
        );

        return new Product(
            id: $data['id'],
            category: $category
        );
    }

    public function findByCategoryCode(string $categoryCode): ProductCollection
    {
        $sql = 'SELECT p.*, c.code as category_code, c.name as category_name 
                FROM products as p 
                    INNER JOIN categories as c on c.id = p.category_id 
                WHERE c.code = :code';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['code' => $categoryCode]);
        $data = $stmt->fetchAll();

        $productList = new ProductCollection();

        foreach ($data as $product) {
            $category = new Category(
                id: $product['category_id'],
                name: $product['category_name'],
                code: $product['category_code'],
            );

            $productList->add(new Product(
                id: $product['id'],
                category: $category
            ));
        }

        return $productList;
    }
}
