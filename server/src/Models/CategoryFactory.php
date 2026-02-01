<?php

namespace App\Models;

class CategoryFactory
{
    public static function create(string $categoryName): AbstractCategory
    {
        return match($categoryName) {
            'all' => new AllCategory($categoryName),
            'clothes' => new ClothesCategory($categoryName),
            'tech' => new TechCategory($categoryName),
            default => throw new \InvalidArgumentException("Unknown category: $name")
        };
    }
}
