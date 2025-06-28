<?php

namespace App\GraphQL\Resolvers;

use App\Models\ProductRepository;

class ProductResolver
{
    private static $repository;

    private static function getRepository(): ProductRepository
    {
        if (!self::$repository) {
            self::$repository = new ProductRepository();
        }
        return self::$repository;
    }

    public static function all(): array
    {
        return self::getRepository()->findAll();
    }

    public static function byCategoryId($categoryId): array
    {
        return self::getRepository()->findByCategory($categoryId);
    }

    public static function getProductById($id)
    {
        return self::getRepository()->findById($id);
    }
}