<?php
/**
 * Product Module Functions
 */

/**
 * Get all products with optional filtering
 */
function getProducts($category_id = null, $search = null, $limit = null, $offset = 0) {
    $db = getDbConnection();
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.deleted_at IS NULL";
    $params = [];
    
    if ($category_id) {
        $sql .= " AND p.category_id = :category_id";
        $params[':category_id'] = $category_id;
    }
    
    if ($search) {
        $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    $sql .= " ORDER BY p.name ASC";
    
    if ($limit) {
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
    }
    
    $stmt = $db->prepare($sql);
    foreach ($params as $param => $value) {
        $stmt->bindValue($param, $value);
    }
    
    $result = $stmt->execute();
    $products = [];
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $products[] = $row;
    }
    
    return $products;
}

/**
 * Get a single product by ID
 */
function getProduct($id) {
    $db = getDbConnection();
    
    $stmt = $db->prepare("SELECT p.*, c.name as category_name 
                         FROM products p 
                         JOIN categories c ON p.category_id = c.id 
                         WHERE p.id = :id AND p.deleted_at IS NULL");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    
    $result = $stmt->execute();
    return $result->fetchArray(SQLITE3_ASSOC);
}

/**
 * Get all categories
 */
function getCategories() {
    $db = getDbConnection();
    
    $result = $db->query("SELECT * FROM categories WHERE deleted_at IS NULL ORDER BY name");
    $categories = [];
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $categories[] = $row;
    }
    
    return $categories;
}

/**
 * Get product count for pagination
 */
function getProductCount($category_id = null, $search = null) {
    $db = getDbConnection();
    
    $sql = "SELECT COUNT(*) as count FROM products WHERE deleted_at IS NULL";
    $params = [];
    
    if ($category_id) {
        $sql .= " AND category_id = :category_id";
        $params[':category_id'] = $category_id;
    }
    
    if ($search) {
        $sql .= " AND (name LIKE :search OR description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    $stmt = $db->prepare($sql);
    foreach ($params as $param => $value) {
        $stmt->bindValue($param, $value);
    }
    
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    return $row['count'];
}

/**
 * Check if product has enough stock
 */
function checkProductStock($product_id, $quantity) {
    $db = getDbConnection();
    
    $stmt = $db->prepare("SELECT stock_quantity FROM products WHERE id = :id");
    $stmt->bindValue(':id', $product_id, SQLITE3_INTEGER);
    
    $result = $stmt->execute();
    $product = $result->fetchArray(SQLITE3_ASSOC);
    
    return $product && $product['stock_quantity'] >= $quantity;
}

/**
 * Format product price
 */
function formatProductPrice($price) {
    return formatMYR($price);
}

/**
 * Get stock status class
 */
function getStockStatusClass($quantity) {
    if ($quantity > 50) {
        return 'stock-high';
    } elseif ($quantity > 20) {
        return 'stock-medium';
    } else {
        return 'stock-low';
    }
} 