<?php
namespace App\Models;

class ProductFactory
{
    public static function create(array $productData): AbstractProduct
    {
        switch (strtolower($productData['category'] ?? '')) {
            case 'clothes':
                return new ClothesProduct($productData);
            case 'tech':
                return new TechProduct($productData);
            default:
                return new TechProduct($productData);
        }
    }
}