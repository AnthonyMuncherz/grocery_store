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


