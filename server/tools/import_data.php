<?php
$host = 'localhost';
$db   = 'sweb-ecommerce';
$user = 'root';
$pass = 'root';
$port = 3306;

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $json = file_get_contents(__DIR__ . '/data.json');
    $data = json_decode($json, true)['data'];

    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (:name)");
    foreach ($data['categories'] as $category) {
        $stmt->execute(['name' => $category['name']]);
    }

    $productStmt = $pdo->prepare("INSERT INTO products (id, name, description, category, brand, in_stock)
                                  VALUES (:id, :name, :description, :category, :brand, :in_stock)");

    $galleryStmt = $pdo->prepare("INSERT INTO product_gallery (product_id, image_url)
                                  VALUES (:product_id, :image_url)");

    $priceStmt = $pdo->prepare("INSERT INTO prices (product_id, currency_label, currency_symbol, amount)
                                VALUES (:product_id, :currency_label, :currency_symbol, :amount)");

    $attributeStmt = $pdo->prepare("INSERT INTO attributes (product_id, attribute_name, attribute_type)
                                    VALUES (:product_id, :attribute_name, :attribute_type)");

    $attributeItemStmt = $pdo->prepare("INSERT INTO attribute_items (attribute_id, value, display_value)
                                        VALUES (:attribute_id, :value, :display_value)");



    foreach ($data['products'] as $product) {
        try {
            $productStmt->execute([
                'id' => $product['id'],
                'name' => $product['name'],
                'description' => $product['description'],
                'category' => $product['category'],
                'brand' => $product['brand'],
                'in_stock' => $product['inStock'] ? 1 : 0
            ]);
        } catch (PDOException $e) {
            echo "Error inserting product: " . $e->getMessage() . "<br>";
        }

        foreach ($product['gallery'] as $url) {
            $galleryStmt->execute([
                'product_id' => $product['id'],
                'image_url' => $url
            ]);
        }

        foreach ($product['prices'] as $price) {
            $priceStmt->execute([
                'product_id' => $product['id'],
                'currency_label' => $price['currency']['label'],
                'currency_symbol' => $price['currency']['symbol'],
                'amount' => $price['amount']
            ]);
        }

        foreach ($product['attributes'] as $attr) {
            $attributeStmt->execute([
                'product_id' => $product['id'],
                'attribute_name' => $attr['name'],
                'attribute_type' => $attr['type']
            ]);
            $attribute_id = $pdo->lastInsertId();

            foreach ($attr['items'] as $item) {
                $attributeItemStmt->execute([
                    'attribute_id' => $attribute_id,
                    'value' => $item['value'],
                    'display_value' => $item['displayValue']
                ]);
            }
        }
    }

    echo "Data imported successfully!xz";

} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage();
}
