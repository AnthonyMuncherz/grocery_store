<?php
require_once __DIR__ . '/config.php';

function getInventoryLogs(): array {
    $db = initializeDatabase();

    $sql = "SELECT il.id, il.product_id, p.name AS product_name, il.action, il.quantity, il.notes, il.created_at
            FROM inventory_logs il
            JOIN products p ON il.product_id = p.id
            ORDER BY il.created_at DESC";

    $result = $db->query($sql);
    $logs = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $logs[] = $row;
    }
    return $logs;
}

function getProductsWithStock(): array {
    $db = initializeDatabase();

    $sql = "SELECT id, name, price, category_id, stock_quantity 
            FROM products 
            WHERE deleted_at IS NULL 
            ORDER BY name ASC";

    $result = $db->query($sql);
    $products = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $products[] = $row;
    }
    return $products;
}


// Fetch a product by ID with current stock info
function getProductById($id) {
    $db = initializeDatabase();
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    return $result->fetchArray(SQLITE3_ASSOC);
}

// Add stock to a product and record inventory log
function addStockToProduct($product_id, $quantity, $notes) {
    $db = initializeDatabase();

    $db->exec('BEGIN TRANSACTION');
    try {
        // Update stock quantity
        $stmt = $db->prepare("UPDATE products SET stock_quantity = stock_quantity + :qty WHERE id = :id");
        $stmt->bindValue(':qty', $quantity, SQLITE3_INTEGER);
        $stmt->bindValue(':id', $product_id, SQLITE3_INTEGER);
        $stmt->execute();

        // Insert inventory log
        $stmt = $db->prepare("INSERT INTO inventory_logs (product_id, action, quantity, notes, created_at) VALUES (:id, 'add', :qty, :notes, datetime('now'))");
        $stmt->bindValue(':id', $product_id, SQLITE3_INTEGER);
        $stmt->bindValue(':qty', $quantity, SQLITE3_INTEGER);
        $stmt->bindValue(':notes', $notes, SQLITE3_TEXT);
        $stmt->execute();

        $db->exec('COMMIT');
        return true;
    } catch (Exception $e) {
        $db->exec('ROLLBACK');
        error_log("Failed to add stock: " . $e->getMessage());
        return false;
    }
}

function getInventoryItems($category_id = null, $search = null) {
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
        $sql .= " AND p.name LIKE :search";
        $params[':search'] = "%$search%";
    }

    $sql .= " ORDER BY p.name ASC";
    $stmt = $db->prepare($sql);

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }

    $result = $stmt->execute();
    $items = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $items[] = $row;
    }

    return $items;
}

function getCategories() {
    $db = getDbConnection();

    $sql = "SELECT id, name FROM categories ORDER BY name ASC";
    $stmt = $db->prepare($sql);
    $result = $stmt->execute();

    $categories = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $categories[] = $row;
    }

    return $categories;
}

function addProduct($product_id, $name, $description, $price, $category_id, $stock_quantity) {
    $db = getDbConnection();

    // If product_id is null or empty, let DB auto-generate (assumes AUTOINCREMENT)
    if ($product_id) {
        $sql = "INSERT INTO products (id, name, description, price, category_id, stock_quantity, created_at) 
                VALUES (:id, :name, :description, :price, :category_id, :stock_quantity, datetime('now'))";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $product_id, SQLITE3_TEXT); // if id is text; change to INTEGER if number
    } else {
        $sql = "INSERT INTO products (name, description, price, category_id, stock_quantity, created_at) 
                VALUES (:name, :description, :price, :category_id, :stock_quantity, datetime('now'))";
        $stmt = $db->prepare($sql);
    }

    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':price', $price, SQLITE3_FLOAT);
    $stmt->bindValue(':category_id', $category_id, SQLITE3_INTEGER);
    $stmt->bindValue(':stock_quantity', $stock_quantity, SQLITE3_INTEGER);

    $result = $stmt->execute();
    return $result !== false;
}
