<?php

namespace App\GraphQL\Resolvers;

use App\Database;

class CategoryResolver
{
    public static function all(): array
        {
            try {
                
                $pdo = Database::connect(); 
                $stmt = $pdo->query("SELECT id, name FROM categories");
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                return $result;
            } catch (\Throwable $e) {
                return [['id' => 0, 'name' => 'DB Error: ' . $e->getMessage()]];
            }
        }

}
