<?php
namespace App\Models;

class CategoryFactory
{
    public static function create(string $categoryName): AbstractCategory
    {
        switch (strtolower($categoryName)) {
            case 'clothes':
                return new ClothesCategory($categoryName);
            case 'tech':
                return new TechCategory($categoryName);
            case 'all':
                return new AllCategory($categoryName);
            default:
                return new AllCategory($categoryName);
        }
    }
}