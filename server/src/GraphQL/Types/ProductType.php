<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Product',
            'fields' => [
                'id' => Type::nonNull(Type::id()),
                'name' => Type::string(),
                'brand' => Type::string(),
                'description' => Type::string(),
                'in_stock' => Type::boolean(),
                'category' => Type::string(),
                'price' => Type::float(),
                'gallery' => Type::listOf(Type::string()),
                'attributes' => Type::listOf(new ObjectType([
                    'name' => 'ProductAttribute',
                    'fields' => [
                        'name' => Type::string(),
                        'options' => Type::listOf(Type::string())
                    ]
                ])),               

            ]
        ]);
    }
}
