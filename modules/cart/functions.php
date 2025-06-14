<?php
/**
 * Cart Functions - Session-based cart system
 */

/**
 * Get current session ID for cart
 */
function getCartSessionId() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['cart_session_id'])) {
        $_SESSION['cart_session_id'] = uniqid('cart_', true);
    }
    
    return $_SESSION['cart_session_id'];
}

/**
 * Add product to cart
 */
function addToCart($product_id, $quantity = 1) {
    try {
        // Validate product exists and has stock
        $product = getProductById($product_id);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }
        
        if ($product['stock_quantity'] < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock available.'];
        }
        
        // Initialize cart in session if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if product already in cart
        if (isset($_SESSION['cart'][$product_id])) {
            $new_quantity = $_SESSION['cart'][$product_id]['quantity'] + $quantity;
            if ($new_quantity > $product['stock_quantity']) {
                return ['success' => false, 'message' => 'Cannot add more items than available stock.'];
            }
            $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
            $_SESSION['cart'][$product_id]['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image_url' => $product['image_url'],
                'stock_quantity' => $product['stock_quantity'],
                'added_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return ['success' => true, 'message' => 'Product added to cart successfully.'];
        
    } catch (Exception $e) {
        error_log("Add to cart error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error adding product to cart.'];
    }
}

/**
 * Update cart item quantity
 */
function updateCartItem($product_id, $quantity) {
    try {
        if (!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$product_id])) {
            return false;
        }
        
        // Validate stock
        $product = getProductById($product_id);
        if ($quantity > $product['stock_quantity']) {
            return false;
        }
        
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        $_SESSION['cart'][$product_id]['updated_at'] = date('Y-m-d H:i:s');
        
        return true;
        
    } catch (Exception $e) {
        error_log("Update cart error: " . $e->getMessage());
        return false;
    }
}

/**
 * Remove product from cart
 */
function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        return true;
    }
    return false;
}

/**
 * Clear entire cart
 */
function clearCart() {
    $_SESSION['cart'] = [];
    return true;
}

/**
 * Get all cart items
 */
function getCartItems() {
    if (!isset($_SESSION['cart'])) {
        return [];
    }
    
    return $_SESSION['cart'];
}

/**
 * Get cart total amount
 */
function getCartTotal() {
    if (!isset($_SESSION['cart'])) {
        return 0.00;
    }
    
    $total = 0.00;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    return $total;
}

/**
 * Get cart item count
 */
function getCartCount() {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    
    return $count;
}

/**
 * Check if cart is empty
 */
function isCartEmpty() {
    return empty($_SESSION['cart']);
}

/**
 * Get product by ID (helper function)
 */
function getProductById($product_id) {
    try {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id AND (deleted_at IS NULL OR deleted_at = '')");
        $stmt->bindParam(':id', $product_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        if ($result) {
            $product = $result->fetchArray(SQLITE3_ASSOC);
            return $product ?: null;
        }
        
        return null;
        
    } catch (Exception $e) {
        error_log("Get product error: " . $e->getMessage());
        return null;
    }
} 