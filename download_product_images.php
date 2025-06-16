<?php
/**
 * Malaysian Grocery Store Product Image Downloader
 * Downloads appropriate images for existing products and updates database
 */

require_once 'config/database.php';
require_once 'includes/functions.php';

// Set time limit for downloading
set_time_limit(0);

// Malaysian grocery product image URLs mapping
$productImageUrls = [
    // Vegetables
    'kangkung' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'water spinach' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'sayur kangkung' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'cili padi' => 'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'bird eye chili' => 'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'lettuce' => 'https://images.unsplash.com/photo-1556801712-76c8eb07bbc9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'tomato' => 'https://images.unsplash.com/photo-1546470427-e2c29e1b4c10?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'cucumber' => 'https://images.unsplash.com/photo-1449300079323-02e209d9d3a6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    
    // Fruits
    'durian' => 'https://images.unsplash.com/photo-1591994843349-f415893b3a6b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'musang king' => 'https://images.unsplash.com/photo-1591994843349-f415893b3a6b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'rambutan' => 'https://images.unsplash.com/photo-1606663889134-b1dedb5ed8b7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'mango' => 'https://images.unsplash.com/photo-1553279768-865429fa0078?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'papaya' => 'https://images.unsplash.com/photo-1617112848923-cc2234396a8d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'banana' => 'https://images.unsplash.com/photo-1574226516831-e1dff420e562?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'apple' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'orange' => 'https://images.unsplash.com/photo-1547514701-42782101795e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    
    // Rice & Grains
    'beras' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'rice' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'siam' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    
    // Beverages
    'milo' => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'milk' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    
    // Oil & Condiments
    'oil' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'cooking oil' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'palm oil' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    
    // Snacks
    'keropok' => 'https://images.unsplash.com/photo-1599599810694-57a2ca8276a8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'crackers' => 'https://images.unsplash.com/photo-1599599810694-57a2ca8276a8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    
    // Default fallback for grocery items
    'default' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80'
];

/**
 * Download image from URL using curl
 */
function downloadImage($imageUrl, $filename) {
    $downloadPath = "assets/images/products/" . $filename;
    
    // Create directory if it doesn't exist
    $dir = dirname($downloadPath);
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Initialize curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $imageUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $imageData !== false) {
        file_put_contents($downloadPath, $imageData);
        return $downloadPath;
    }
    
    return false;
}

/**
 * Find best matching image URL for product name
 */
function findBestImageUrl($productName, $imageUrls) {
    $name = strtolower($productName);
    
    // Check for exact matches first
    foreach ($imageUrls as $keyword => $url) {
        if (strpos($name, $keyword) !== false) {
            return $url;
        }
    }
    
    // Return default if no match found
    return $imageUrls['default'];
}

/**
 * Generate filename from product name
 */
function generateFilename($productName) {
    $name = strtolower($productName);
    $name = preg_replace('/[^a-z0-9\s]/', '', $name);
    $name = preg_replace('/\s+/', '_', trim($name));
    return $name . '.jpg';
}

try {
    echo "<h2>Malaysian Grocery Store - Product Image Downloader</h2>\n";
    echo "<p>Starting image download process...</p>\n";
    
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // Fetch all products without images
    $stmt = $pdo->prepare("SELECT id, name, image_url FROM products WHERE deleted_at IS NULL ORDER BY id");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($products)) {
        echo "<p>No products found in database.</p>\n";
        exit;
    }
    
    echo "<p>Found " . count($products) . " products to process.</p>\n";
    echo "<ul>\n";
    
    $updated = 0;
    $downloaded = 0;
    
    foreach ($products as $product) {
        $productId = $product['id'];
        $productName = $product['name'];
        $currentImageUrl = $product['image_url'];
        
        echo "<li><strong>Product #{$productId}: {$productName}</strong><br>\n";
        
        // Skip if product already has an image
        if (!empty($currentImageUrl) && file_exists($currentImageUrl)) {
            echo "  ✓ Already has image: {$currentImageUrl}</li>\n";
            continue;
        }
        
        // Find best matching image URL
        $imageUrl = findBestImageUrl($productName, $productImageUrls);
        $filename = generateFilename($productName);
        
        echo "  📥 Downloading from: {$imageUrl}<br>\n";
        echo "  💾 Saving as: {$filename}<br>\n";
        
        // Download the image
        $downloadPath = downloadImage($imageUrl, $filename);
        
        if ($downloadPath) {
            // Update database with new image path
            $updateStmt = $pdo->prepare("UPDATE products SET image_url = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $updateStmt->execute([$downloadPath, $productId]);
            
            echo "  ✅ Downloaded and updated database<br>\n";
            $downloaded++;
            $updated++;
        } else {
            echo "  ❌ Failed to download image<br>\n";
        }
        
        echo "</li>\n";
        
        // Small delay to be respectful to the image server
        usleep(500000); // 0.5 seconds
    }
    
    echo "</ul>\n";
    echo "<h3>Summary</h3>\n";
    echo "<p>✅ Images downloaded: {$downloaded}</p>\n";
    echo "<p>📊 Database records updated: {$updated}</p>\n";
    echo "<p>🎉 Process completed successfully!</p>\n";
    
    // Display sample of updated products
    $sampleStmt = $pdo->prepare("SELECT id, name, image_url FROM products WHERE image_url IS NOT NULL AND deleted_at IS NULL LIMIT 5");
    $sampleStmt->execute();
    $sampleProducts = $sampleStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($sampleProducts)) {
        echo "<h3>Sample of Updated Products</h3>\n";
        echo "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>\n";
        
        foreach ($sampleProducts as $product) {
            if (file_exists($product['image_url'])) {
                echo "<div style='border: 1px solid #ddd; padding: 10px; width: 200px;'>\n";
                echo "<img src='{$product['image_url']}' alt='{$product['name']}' style='width: 100%; height: 150px; object-fit: cover;'><br>\n";
                echo "<strong>{$product['name']}</strong><br>\n";
                echo "<small>#{$product['id']}</small>\n";
                echo "</div>\n";
            }
        }
        
        echo "</div>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}

h2 {
    color: #2563eb;
    border-bottom: 2px solid #ddd;
    padding-bottom: 10px;
}

h3 {
    color: #059669;
    margin-top: 30px;
}

li {
    margin-bottom: 10px;
    padding: 5px;
    background: #f8f9fa;
    border-radius: 5px;
}

.error {
    background: #fef2f2;
    color: #dc2626;
    padding: 10px;
    border-radius: 5px;
    border-left: 4px solid #dc2626;
}

.success {
    background: #f0fdf4;
    color: #16a34a;
    padding: 10px;
    border-radius: 5px;
    border-left: 4px solid #16a34a;
}
</style> 