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
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id AND deleted_at IS NULL");
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

// Update product stock (add or remove) with proper logging and validation
function updateProductStock($product_id, $adjustment, $notes = '') {
    $db = initializeDatabase();

    try {
        $db->exec('BEGIN TRANSACTION');

        // Check current stock first
        $stmt = $db->prepare("SELECT stock_quantity, name FROM products WHERE id = :id AND deleted_at IS NULL");
        $stmt->bindValue(':id', $product_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        
        if (!$row) {
            throw new Exception("Product not found or has been deleted");
        }
        
        $current_stock = intval($row['stock_quantity']);
        $product_name = $row['name'];
        $new_stock = $current_stock + $adjustment;
        
        // Prevent negative stock
        if ($new_stock < 0) {
            throw new Exception("Cannot reduce stock below zero. Current stock: $current_stock, attempted reduction: " . abs($adjustment));
        }

        // Update stock quantity with updated_at timestamp
        $stmt = $db->prepare("UPDATE products SET stock_quantity = :new_stock, updated_at = datetime('now') WHERE id = :id");
        $stmt->bindValue(':new_stock', $new_stock, SQLITE3_INTEGER);
        $stmt->bindValue(':id', $product_id, SQLITE3_INTEGER);
        $stmt->execute();

        // Log the inventory change with enhanced notes
        $action = $adjustment > 0 ? 'add' : 'remove';
        $enhanced_notes = $notes;
        if (empty($enhanced_notes)) {
            $enhanced_notes = $adjustment > 0 ? 'Stock increase' : 'Stock reduction';
        }
        $enhanced_notes .= " (Previous: $current_stock, New: $new_stock)";
        
        $stmt = $db->prepare("INSERT INTO inventory_logs (product_id, action, quantity, notes, created_at) 
                             VALUES (:id, :action, :qty, :notes, datetime('now'))");
        $stmt->bindValue(':id', $product_id, SQLITE3_INTEGER);
        $stmt->bindValue(':action', $action, SQLITE3_TEXT);
        $stmt->bindValue(':qty', abs($adjustment), SQLITE3_INTEGER);
        $stmt->bindValue(':notes', $enhanced_notes, SQLITE3_TEXT);
        $stmt->execute();

        $db->exec('COMMIT');
        
        // Log successful operation
        logDebug("Stock updated successfully for product '$product_name' (ID: $product_id). Adjustment: $adjustment, New stock: $new_stock");
        
        return true;

    } catch (Exception $e) {
        $db->exec('ROLLBACK');
        $error_msg = "Failed to update stock for product $product_id: " . $e->getMessage();
        error_log($error_msg);
        logError($error_msg);
        return false;
    }
}

function getInventoryItems($category_id = null, $search = null) {
    $db = initializeDatabase();

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
    $db = initializeDatabase();

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
    $db = initializeDatabase();

    try {
        $db->exec('BEGIN TRANSACTION');

        // Validate inputs
        if (empty(trim($name))) {
            throw new Exception("Product name cannot be empty");
        }
        if ($price <= 0) {
            throw new Exception("Price must be greater than zero");
        }
        if ($category_id <= 0) {
            throw new Exception("Valid category must be selected");
        }
        if ($stock_quantity < 0) {
            throw new Exception("Stock quantity cannot be negative");
        }

        // If product_id is provided, check if it already exists
        if ($product_id) {
            $stmt = $db->prepare("SELECT id FROM products WHERE id = :id");
            $stmt->bindValue(':id', $product_id, SQLITE3_TEXT);
            $result = $stmt->execute();
            if ($result->fetchArray(SQLITE3_ASSOC)) {
                throw new Exception("Product ID already exists");
            }
        }

        // Insert product
        if ($product_id) {
            $sql = "INSERT INTO products (id, name, description, price, category_id, stock_quantity, created_at, updated_at) 
                    VALUES (:id, :name, :description, :price, :category_id, :stock_quantity, datetime('now'), datetime('now'))";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $product_id, SQLITE3_TEXT);
        } else {
            $sql = "INSERT INTO products (name, description, price, category_id, stock_quantity, created_at, updated_at) 
                    VALUES (:name, :description, :price, :category_id, :stock_quantity, datetime('now'), datetime('now'))";
            $stmt = $db->prepare($sql);
        }

        $stmt->bindValue(':name', trim($name), SQLITE3_TEXT);
        $stmt->bindValue(':description', trim($description), SQLITE3_TEXT);
        $stmt->bindValue(':price', $price, SQLITE3_FLOAT);
        $stmt->bindValue(':category_id', $category_id, SQLITE3_INTEGER);
        $stmt->bindValue(':stock_quantity', $stock_quantity, SQLITE3_INTEGER);

        $result = $stmt->execute();
        
        if ($result) {
            $final_product_id = $product_id ?: $db->lastInsertRowID();
            
            // Log initial stock if any
            if ($stock_quantity > 0) {
                $stmt = $db->prepare("INSERT INTO inventory_logs (product_id, action, quantity, notes, created_at) 
                                     VALUES (:id, 'add', :qty, :notes, datetime('now'))");
                $stmt->bindValue(':id', $final_product_id, SQLITE3_INTEGER);
                $stmt->bindValue(':qty', $stock_quantity, SQLITE3_INTEGER);
                $stmt->bindValue(':notes', 'Initial stock - Product created', SQLITE3_TEXT);
                $stmt->execute();
            }
            
            $db->exec('COMMIT');
            
            // Log successful operation
            logDebug("Product added successfully: '$name' (ID: $final_product_id) with initial stock: $stock_quantity");
            
            return true;
        } else {
            $db->exec('ROLLBACK');
            return false;
        }

    } catch (Exception $e) {
        $db->exec('ROLLBACK');
        $error_msg = "Failed to add product '$name': " . $e->getMessage();
        error_log($error_msg);
        logError($error_msg);
        return false;
    }
}

// Get low stock products (stock <= threshold)
function getLowStockProducts($threshold = 10) {
    $db = initializeDatabase();
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.deleted_at IS NULL AND p.stock_quantity <= :threshold 
            ORDER BY p.stock_quantity ASC, p.name ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':threshold', $threshold, SQLITE3_INTEGER);
    $result = $stmt->execute();
    
    $products = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $products[] = $row;
    }
    
    return $products;
}

// Get inventory summary statistics
function getInventoryStats() {
    $db = initializeDatabase();
    
    $stats = [];
    
    // Total products
    $result = $db->query("SELECT COUNT(*) as total FROM products WHERE deleted_at IS NULL");
    $stats['total_products'] = $result->fetchArray(SQLITE3_ASSOC)['total'];
    
    // Total stock value
    $result = $db->query("SELECT SUM(price * stock_quantity) as total_value FROM products WHERE deleted_at IS NULL");
    $stats['total_value'] = $result->fetchArray(SQLITE3_ASSOC)['total_value'] ?? 0;
    
    // Low stock count (<=10)
    $result = $db->query("SELECT COUNT(*) as low_stock FROM products WHERE deleted_at IS NULL AND stock_quantity <= 10");
    $stats['low_stock_count'] = $result->fetchArray(SQLITE3_ASSOC)['low_stock'];
    
    // Out of stock count
    $result = $db->query("SELECT COUNT(*) as out_of_stock FROM products WHERE deleted_at IS NULL AND stock_quantity = 0");
    $stats['out_of_stock_count'] = $result->fetchArray(SQLITE3_ASSOC)['out_of_stock'];
    
    return $stats;
}
