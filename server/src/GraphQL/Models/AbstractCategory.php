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

    public function getProducts(): array
    {
        if ($this->name === 'all') {
            $stmt = $this->pdo->query("SELECT * FROM products");
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category = ?");
            $stmt->execute([$this->name]);
        }
        
        $productsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $products = [];
        foreach ($productsData as $productData) {
            $product = ProductFactory::create($productData);
            $product->loadRelations();
            $products[] = $product->toArray();
        }
        
        return $products;
    }
}