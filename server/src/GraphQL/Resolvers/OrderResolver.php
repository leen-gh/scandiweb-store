<?php
namespace App\GraphQL\Resolvers;

use App\Database;

class OrderResolver {
    public static function place($args) {
        $pdo = Database::connect(); 

        $items = json_encode($args['items']);
        $total = $args['totalPrice'];

        $stmt = $pdo->prepare("INSERT INTO orders (items, total_price) VALUES (?, ?)");
        $stmt->execute([$items, $total]);

        return [
            'id' => $pdo->lastInsertId(),
            'items' => $items,
            'total_price' => $total,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
}
