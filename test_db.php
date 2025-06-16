<?php
/**
 * Simple database test script
 */

echo "Testing database connection...\n";

try {
    require_once 'config/database.php';
    
    $pdo = getDatabaseConnection();
    echo "✓ Database connected successfully\n";
    
    // Test query
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE deleted_at IS NULL");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "✓ Found " . $result['count'] . " products in database\n";
    
    // Show first few products
    $stmt = $pdo->prepare("SELECT id, name, image_url FROM products WHERE deleted_at IS NULL LIMIT 3");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nFirst 3 products:\n";
    foreach ($products as $product) {
        echo "  - #{$product['id']}: {$product['name']} (image: " . ($product['image_url'] ?? 'null') . ")\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?> 