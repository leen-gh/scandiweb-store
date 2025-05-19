<?php

namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

use App\GraphQL\Types\CategoryType;
use App\GraphQL\Types\ProductType;
use App\GraphQL\Resolvers\CategoryResolver;
use App\GraphQL\Resolvers\ProductResolver;


class GraphQL {
    static public function handle() {
        $categoryType = new CategoryType();
        $productType = new ProductType();
        
        try {
            
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf($categoryType),
                        'resolve' => fn () => CategoryResolver::all()
                    ],
                    'products' => [
                        'type' => Type::listOf($productType),
                        'resolve' => fn () => ProductResolver::all()
                    ],
                    'categoryProducts' => [
                        'type' => Type::listOf($productType),
                        'args' => [
                            'category' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => fn ($root, $args) => ProductResolver::byCategoryId($args['category']),
                    ],                    
                    'product' => [
                        'type' => $productType,
                        'args' => [
                            'id' => Type::nonNull(Type::id()),
                        ],
                        'resolve' => fn ($root, $args) => ProductResolver::GetProductById($args['id']),
                    ],


                ],
            ]);
            
            $schema = new Schema(
                (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
            );
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
            $query = $input['query'] ?? null;
            $variables = $input['variables'] ?? null;

            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variables);
            $output = $result->toArray();
            
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'error' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ],
            ]);
            exit;
        }
        

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}