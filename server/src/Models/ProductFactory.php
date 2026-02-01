<?php

namespace App\Models;

class ProductFactory
{
    public static function create(array $productData): AbstractProduct
    {

        return match (strtolower($productData['category'] ?? '')) {
            'clothes' => new ClothesProduct($productData),
            'tech' => new TechProduct($productData),
            default => throw new \InvalidArgumentException(
                "Unknown product category: " . ($productData['category'] ?? 'none')
            )
        };

    }
}
