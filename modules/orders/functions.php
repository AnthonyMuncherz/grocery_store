<?php
/**
 * Orders module helper functions
 */

function createOrder($customer_name, $customer_email, $customer_phone, $shipping_address, $total_amount, $status, $payment_method, $order_items) {
    global $db;

    // Start transaction
    $db->exec('BEGIN TRANSACTION');

    try {
        // Insert into orders table
        $stmt = $db->prepare("INSERT INTO orders 
            (customer_name, customer_email, customer_phone, shipping_address, total_amount, status, payment_method, created_at, updated_at) 
            VALUES 
            (:customer_name, :customer_email, :customer_phone, :shipping_address, :total_amount, :status, :payment_method,  :created_at, :updated_at)");
        
        $now = date('Y-m-d H:i:s');

        $stmt->bindValue(':customer_name', $customer_name, SQLITE3_TEXT);
        $stmt->bindValue(':customer_email', $customer_email, SQLITE3_TEXT);
        $stmt->bindValue(':customer_phone', $customer_phone, SQLITE3_TEXT);
        $stmt->bindValue(':shipping_address', $shipping_address, SQLITE3_TEXT);
        $stmt->bindValue(':total_amount', $total_amount, SQLITE3_FLOAT);
        $stmt->bindValue(':status', $status, SQLITE3_TEXT);
        $stmt->bindValue(':payment_method', $payment_method, SQLITE3_TEXT);
       // $stmt->bindValue(':payment_status', $payment_status, SQLITE3_TEXT);
        $stmt->bindValue(':created_at', $now, SQLITE3_TEXT);
        $stmt->bindValue(':updated_at', $now, SQLITE3_TEXT);

        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Failed to create order.");
        }

        $order_id = $db->lastInsertRowID();

        // Insert order items
        $stmt_item = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, created_at) VALUES (:order_id, :product_id, :quantity, :unit_price, :created_at)");

        foreach ($order_items as $item) {
            // Validate quantity and unit_price (optional, but good practice)
            $quantity = (int)$item['quantity'];
            $unit_price = (float)$item['unit_price'];

            if ($quantity <= 0) {
                throw new Exception("Invalid quantity for product ID {$item['product_id']}");
            }

            $stmt_item->bindValue(':order_id', $order_id, SQLITE3_INTEGER);
            $stmt_item->bindValue(':product_id', $item['product_id'], SQLITE3_INTEGER);
            $stmt_item->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
            $stmt_item->bindValue(':unit_price', $unit_price, SQLITE3_FLOAT);
            $stmt_item->bindValue(':created_at', $now, SQLITE3_TEXT);

            $res = $stmt_item->execute();
            if (!$res) {
                throw new Exception("Failed to insert order item for product ID {$item['product_id']}.");
            }

            // Update stock quantity safely with prepared statement to avoid injection risk
            $update_stock = $db->prepare("UPDATE products SET stock_quantity = stock_quantity - :quantity WHERE id = :product_id");
            $update_stock->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
            $update_stock->bindValue(':product_id', $item['product_id'], SQLITE3_INTEGER);
            $update_stock->execute();
        }

        // Commit transaction
        $db->exec('COMMIT');

        return $order_id;

    } catch (Exception $e) {
        // Rollback on error
        $db->exec('ROLLBACK');
        throw $e;
    }
}

/**
 * Get all orders with pagination
 */
function getAllOrders($limit = 20, $offset = 0, $orderBy = 'created_at', $orderDir = 'DESC') {
    global $db;
    
    // Validate sort parameters
    $validColumns = ['id', 'customer_name', 'customer_email', 'total_amount', 'status', 'payment_method', 'created_at', 'updated_at'];
    $validDirections = ['ASC', 'DESC'];
    
    if (!in_array($orderBy, $validColumns)) {
        $orderBy = 'created_at';
    }
    if (!in_array($orderDir, $validDirections)) {
        $orderDir = 'DESC';
    }
    
    $sql = "SELECT * FROM orders ORDER BY $orderBy $orderDir LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, SQLITE3_INTEGER);
    $stmt->bindValue(':offset', (int)$offset, SQLITE3_INTEGER);
    
    $result = $stmt->execute();
    $orders = [];
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $orders[] = $row;
    }
    
    return $orders;
}

/**
 * Get total order count
 */
function getTotalOrdersCount() {
    global $db;
    
    $result = $db->query("SELECT COUNT(*) as count FROM orders");
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    return (int)$row['count'];
}

/**
 * Get order by ID with order items
 */
function getOrderById($order_id) {
    global $db;
    
    // Get order details
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = :id");
    $stmt->bindValue(':id', (int)$order_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    
    $order = $result->fetchArray(SQLITE3_ASSOC);
    if (!$order) {
        return null;
    }
    
    // Get order items with product details
    $stmt = $db->prepare("
        SELECT oi.*, p.name as product_name, p.image_url 
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = :order_id
    ");
    $stmt->bindValue(':order_id', (int)$order_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    
    $order_items = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $order_items[] = $row;
    }
    
    $order['items'] = $order_items;
    
    return $order;
}

/**
 * Update order status
 */
function updateOrderStatus($order_id, $status) {
    global $db;
    
    $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception("Invalid order status");
    }
    
    $stmt = $db->prepare("UPDATE orders SET status = :status, updated_at = :updated_at WHERE id = :id");
    $stmt->bindValue(':status', $status, SQLITE3_TEXT);
    $stmt->bindValue(':updated_at', date('Y-m-d H:i:s'), SQLITE3_TEXT);
    $stmt->bindValue(':id', (int)$order_id, SQLITE3_INTEGER);
    
    return $stmt->execute();
}

/**
 * Update entire order
 */
function updateOrder($order_id, $customer_name, $customer_email, $customer_phone, $shipping_address, $total_amount, $status, $payment_method, $order_items) {
    global $db;
    
    // Start transaction
    $db->exec('BEGIN TRANSACTION');
    
    try {
        // Update order details
        $stmt = $db->prepare("UPDATE orders SET 
            customer_name = :customer_name, 
            customer_email = :customer_email, 
            customer_phone = :customer_phone, 
            shipping_address = :shipping_address, 
            total_amount = :total_amount, 
            status = :status, 
            payment_method = :payment_method, 
            updated_at = :updated_at 
            WHERE id = :id");
        
        $now = date('Y-m-d H:i:s');
        
        $stmt->bindValue(':customer_name', $customer_name, SQLITE3_TEXT);
        $stmt->bindValue(':customer_email', $customer_email, SQLITE3_TEXT);
        $stmt->bindValue(':customer_phone', $customer_phone, SQLITE3_TEXT);
        $stmt->bindValue(':shipping_address', $shipping_address, SQLITE3_TEXT);
        $stmt->bindValue(':total_amount', $total_amount, SQLITE3_FLOAT);
        $stmt->bindValue(':status', $status, SQLITE3_TEXT);
        $stmt->bindValue(':payment_method', $payment_method, SQLITE3_TEXT);
        $stmt->bindValue(':updated_at', $now, SQLITE3_TEXT);
        $stmt->bindValue(':id', (int)$order_id, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Failed to update order.");
        }
        
        // Delete existing order items
        deleteOrderItems($order_id);
        
        // Insert new order items
        $stmt_item = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, created_at) VALUES (:order_id, :product_id, :quantity, :unit_price, :created_at)");
        
        foreach ($order_items as $item) {
            $quantity = (int)$item['quantity'];
            $unit_price = (float)$item['unit_price'];
            
            if ($quantity <= 0) {
                throw new Exception("Invalid quantity for product ID {$item['product_id']}");
            }
            
            $stmt_item->bindValue(':order_id', (int)$order_id, SQLITE3_INTEGER);
            $stmt_item->bindValue(':product_id', $item['product_id'], SQLITE3_INTEGER);
            $stmt_item->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
            $stmt_item->bindValue(':unit_price', $unit_price, SQLITE3_FLOAT);
            $stmt_item->bindValue(':created_at', $now, SQLITE3_TEXT);
            
            $res = $stmt_item->execute();
            if (!$res) {
                throw new Exception("Failed to insert order item for product ID {$item['product_id']}.");
            }
        }
        
        // Commit transaction
        $db->exec('COMMIT');
        
        return true;
        
    } catch (Exception $e) {
        // Rollback on error
        $db->exec('ROLLBACK');
        throw $e;
    }
}

/**
 * Delete order items for a specific order
 */
function deleteOrderItems($order_id) {
    global $db;
    
    $stmt = $db->prepare("DELETE FROM order_items WHERE order_id = :order_id");
    $stmt->bindValue(':order_id', (int)$order_id, SQLITE3_INTEGER);
    
    return $stmt->execute();
}

/**
 * Delete order and its items
 */
function deleteOrder($order_id) {
    global $db;
    
    // Start transaction
    $db->exec('BEGIN TRANSACTION');
    
    try {
        // Delete order items first (foreign key constraint)
        deleteOrderItems($order_id);
        
        // Delete the order
        $stmt = $db->prepare("DELETE FROM orders WHERE id = :id");
        $stmt->bindValue(':id', (int)$order_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception("Failed to delete order.");
        }
        
        // Commit transaction
        $db->exec('COMMIT');
        
        return true;
        
    } catch (Exception $e) {
        // Rollback on error
        $db->exec('ROLLBACK');
        throw $e;
    }
}

/**
 * Check if order exists
 */
function orderExists($order_id) {
    global $db;
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM orders WHERE id = :id");
    $stmt->bindValue(':id', (int)$order_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    return (int)$row['count'] > 0;
}

function getProducts() {
    global $db; // your SQLite3 database connection
    
    $stmt = $db->prepare("SELECT * FROM products");
    $result = $stmt->execute();

    $products = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $products[] = $row;
    }

    return $products;
}

if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        $db = new SQLite3(__DIR__ . '/../../database/grocery_store.db');
        $db->enableExceptions(true);
        return $db;
    }
}


