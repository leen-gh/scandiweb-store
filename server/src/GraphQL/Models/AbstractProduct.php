<?php

namespace App\Models;

use App\Database;

abstract class AbstractProduct
{
    protected $pdo;
    protected $id;
    protected $name;
    protected $brand;
    protected $description;
    protected $inStock;
    protected $category;
    protected $price;
    protected $gallery = [];
    protected $attributes = [];

    public function __construct(array $data)
    {
        $this->pdo = Database::connect();
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->brand = $data['brand'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->inStock = $data['in_stock'] ?? false;
        $this->category = $data['category'] ?? null;
    }

    abstract protected function getSpecificAttributes(): array;

    public function loadRelations(): void
    {
        $this->loadPrice();
        $this->loadGallery();
        $this->loadAttributes();
    }

    private function loadPrice(): void
    {
        $stmt = $this->pdo->prepare("SELECT amount FROM prices WHERE product_id = ? LIMIT 1");
        $stmt->execute([$this->id]);
        $priceRow = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->price = $priceRow ? floatval($priceRow['amount']) : null;
    }

    private function loadGallery(): void
    {
        $stmt = $this->pdo->prepare("SELECT image_url FROM product_gallery WHERE product_id = ?");
        $stmt->execute([$this->id]);
        $this->gallery = array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'image_url');
    }

    private function loadAttributes(): void
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT attribute_name FROM attributes WHERE product_id = ?");
        $stmt->execute([$this->id]);
        $attributeNames = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $attributes = [];
        foreach ($attributeNames as $name) {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT ai.display_value 
                FROM attributes a
                JOIN attribute_items ai ON a.id = ai.attribute_id
                WHERE a.product_id = ? AND a.attribute_name = ?
            ");
            $stmt->execute([$this->id, $name]);
            $options = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            $attributes[] = [
                'name' => $name,
                'options' => $options
            ];
        }
        $this->attributes = array_merge($attributes, $this->getSpecificAttributes());
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand,
            'description' => $this->description,
            'in_stock' => $this->inStock,
            'category' => $this->category,
            'price' => $this->price,
            'gallery' => $this->gallery,
            'attributes' => $this->attributes
        ];
    }
}