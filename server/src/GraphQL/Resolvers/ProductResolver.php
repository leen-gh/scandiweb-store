<?php

namespace App\GraphQL\Resolvers;

use App\Database;

class ProductResolver
{
    public static function all(): array
    {
        try {
            $pdo = Database::connect();            
            $stmt = $pdo->query("SELECT id, name, brand, description, in_stock, category FROM products");
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\Throwable $e) {
            return [['id' => 0, 'name' => 'DB Error: ' . $e->getMessage()]];
        }
    }

   public static function byCategoryId($categoryId): array
    {
        $pdo = Database::connect();
        $results = [];

        $priceQuery = "
            SELECT p.id, pp.amount AS price
            FROM products p
            LEFT JOIN prices pp ON p.id = pp.product_id
            " . ($categoryId !== 'all' ? "WHERE p.category = ?" : "");
        $stmt = $pdo->prepare($priceQuery);
        $stmt->execute($categoryId !== 'all' ? [$categoryId] : []);
        $prices = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $priceData = [];
        foreach ($prices as $price) {
            $priceData[$price['id']] = $price['price'];
        }

        $galleryQuery = "
            SELECT p.id, GROUP_CONCAT(pg.image_url) AS gallery
            FROM products p
            LEFT JOIN product_gallery pg ON p.id = pg.product_id
            " . ($categoryId !== 'all' ? "WHERE p.category = ?" : "") . "
            GROUP BY p.id";
        $stmt = $pdo->prepare($galleryQuery);
        $stmt->execute($categoryId !== 'all' ? [$categoryId] : []);
        $galleries = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $galleryData = [];
        foreach ($galleries as $gallery) {
            $galleryData[$gallery['id']] = isset($gallery['gallery']) ? explode(',', $gallery['gallery']) : [];
        }

        $attrQuery = "
            SELECT a.product_id, a.attribute_name AS name, ai.display_value AS option_value
            FROM attributes a
            JOIN attribute_items ai ON a.id = ai.attribute_id
        ";
        $stmt = $pdo->query($attrQuery);
        $attributes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $attributeData = [];
        foreach ($attributes as $attr) {
            $productId = $attr['product_id'];
            $name = $attr['name'];
            $option = $attr['option_value'];
            
            if (!isset($attributeData[$productId])) {
                $attributeData[$productId] = [];
            }

            if (!isset($attributeData[$productId][$name])) {
                $attributeData[$productId][$name] = [];
            }

            if (!in_array($option, $attributeData[$productId][$name])) {
                $attributeData[$productId][$name][] = $option;
            }
        }

        $formattedAttributes = [];
        foreach ($attributeData as $productId => $attrs) {
            $formattedAttributes[$productId] = [];
            foreach ($attrs as $name => $options) {
                $formattedAttributes[$productId][] = [
                    'name' => $name,
                    'options' => $options,
                ];
            }
        }

        $stmt = $pdo->prepare("
            SELECT p.*
            FROM products p
            " . ($categoryId !== 'all' ? "WHERE p.category = ?" : ""));
        $stmt->execute($categoryId !== 'all' ? [$categoryId] : []);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            $id = $product['id'];
            $product['price'] = $priceData[$id] ?? null;
            $product['gallery'] = $galleryData[$id] ?? [];
            $product['attributes'] = $formattedAttributes[$id] ?? [];
        }

        return $products;
    }




    public static function getProductById($id)
    {
        try {
            $pdo = Database::connect();

            // Get main product data
            $stmt = $pdo->prepare("SELECT id, name, brand, description, in_stock, category FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$product) return null;

            // Get gallery
            $stmt = $pdo->prepare("SELECT image_url FROM product_gallery WHERE product_id = ?");
            $stmt->execute([$id]);
            $product['gallery'] = array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'image_url');

            // Get prices
            $stmt = $pdo->prepare("SELECT amount FROM prices WHERE product_id = ? LIMIT 1");
            $stmt->execute([$id]);
            $priceRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            $product['price'] = $priceRow ? floatval($priceRow['amount']) : null;

            // Get attributes
            $stmt = $pdo->prepare("SELECT DISTINCT attribute_name FROM attributes WHERE product_id = ?");
            $stmt->execute([$id]);
            $attributeNames = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            $attributes = [];
            foreach ($attributeNames as $name) {
                $stmt = $pdo->prepare("
                    SELECT DISTINCT ai.display_value 
                    FROM attributes a
                    JOIN attribute_items ai ON a.id = ai.attribute_id
                    WHERE a.product_id = ? AND a.attribute_name = ?
                ");
                $stmt->execute([$id, $name]);
                $options = $stmt->fetchAll(\PDO::FETCH_COLUMN);

                $attributes[] = [
                    'name' => $name,
                    'options' => $options
                ];
            }
            $product['attributes'] = $attributes;

            

            return $product;

        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }


}