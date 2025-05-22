<?php
namespace App\GraphQL\Types;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class OrderItemInputType extends InputObjectType {
    public function __construct() {
        parent::__construct([
            'name' => 'OrderItemInput',
            'fields' => [
                'name' => Type::nonNull(Type::string()),
                'price' => Type::nonNull(Type::float()),
                'quantity' => Type::nonNull(Type::int()),
                'selectedAttributes' => Type::nonNull(Type::string()), // JSON string
            ],
        ]);
    }
}
