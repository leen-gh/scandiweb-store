<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderType extends ObjectType {
    public function __construct() {
        parent::__construct([
            'name' => 'Order',
            'fields' => [
                'id' => Type::id(),
                'items' => Type::string(), 
                'total_price' => Type::float(),
                'created_at' => Type::string(),
            ],
        ]);
    }
}
