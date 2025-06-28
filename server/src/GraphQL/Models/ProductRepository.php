<?php
namespace App\Models;

use App\Database;


class ProductRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM products");
            $productsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $products = [];
            foreach ($productsData as $productData) {
                
                $product = ProductFactory::create($productData);
                $product->loadRelations();
                $products[] = $product->toArray();
            }
            
            return $products;
        } catch (\Throwable $e) {
            return [['id' => 0, 'name' => 'DB Error: ' . $e->getMessage()]];
        }
    }

    public function findById($id): ?array 
    {
        try {

            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $productData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$productData) return null;


            $product = ProductFactory::create($productData);
            $product->loadRelations();
            
            return $product->toArray();
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function findByCategory(string $categoryId): array
    {
        $category = CategoryFactory::create($categoryId);
        return $category->getProducts();
    }
}
