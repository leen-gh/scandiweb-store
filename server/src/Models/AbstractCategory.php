<?php

namespace App\Models;

use App\Database;

abstract class AbstractCategory
{
    protected $pdo;
    protected $name;

    public function __construct(string $name)
    {
        $this->pdo = Database::connect();
        $this->name = $name;
    }

    abstract protected function buildQuery(): array;

    public function getProducts(): array
    {
        [$sql, $params] = $this->buildQuery();

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $productsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->transformProducts($productsData);

    }

    protected function transformProducts(array $productsData): array
    {
        $products = [];
        foreach ($productsData as $productData) {
            $product = ProductFactory::create($productData);
            $product->loadRelations();
            $products[] = $product->toArray();
        }
        return $products;
    }
}
