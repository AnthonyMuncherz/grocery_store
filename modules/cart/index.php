<?php
/**
 * Cart Module - Main Entry Point
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/constants.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../products/functions.php';

// Get action parameter
$action = $_GET['action'] ?? 'view';

try {
    switch ($action) {
        case 'add':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $product_id = intval($_POST['product_id']);
                $quantity = intval($_POST['quantity']) ?: 1;
                
                if ($product_id > 0) {
                    $result = addToCart($product_id, $quantity);
                    if ($result['success']) {
                        $_SESSION['success_message'] = 'Product added to cart successfully!';
                    } else {
                        $_SESSION['error_message'] = $result['message'];
                    }
                }
                header('Location: index.php?module=cart');
                exit;
            }
            break;
            
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $product_id = intval($_POST['product_id']);
                $quantity = intval($_POST['quantity']);
                
                if ($product_id > 0 && $quantity > 0) {
                    updateCartItem($product_id, $quantity);
                    $_SESSION['success_message'] = 'Cart updated successfully!';
                } else {
                    $_SESSION['error_message'] = 'Invalid quantity specified.';
                }
                header('Location: index.php?module=cart');
                exit;
            }
            break;
            
        case 'remove':
            $product_id = intval($_GET['product_id']);
            if ($product_id > 0) {
                removeFromCart($product_id);
                $_SESSION['success_message'] = 'Product removed from cart.';
            }
            header('Location: index.php?module=cart');
            exit;
            
        case 'clear':
            clearCart();
            $_SESSION['success_message'] = 'Cart cleared successfully.';
            header('Location: index.php?module=cart');
            exit;
            
        case 'debug_clear':
            // Debug action to clear corrupted cart data
            unset($_SESSION['cart']);
            unset($_SESSION['cart_session_id']);
            $_SESSION['success_message'] = 'Cart data reset successfully.';
            header('Location: index.php?module=cart');
            exit;
            
        case 'view':
        default:
            // Get cart items and total
            $cart_items = getCartItems();
            
            // Validate cart items and fix any missing data
            foreach ($cart_items as $product_id => &$item) {
                // Ensure all required fields exist
                if (!isset($item['id']) || !isset($item['name']) || !isset($item['price']) || !isset($item['quantity'])) {
                    // Remove invalid cart item
                    unset($_SESSION['cart'][$product_id]);
                    continue;
                }
                
                // Set default values for optional fields
                if (!isset($item['image_url'])) {
                    $item['image_url'] = 'default.jpg';
                }
                if (!isset($item['stock_quantity'])) {
                    // Get fresh stock data from database
                    $product = getProductById($product_id);
                    $item['stock_quantity'] = $product ? $product['stock_quantity'] : 0;
                }
            }
            unset($item); // Break reference
            
            // Refresh cart items after validation
            $cart_items = getCartItems();
            $cart_total = getCartTotal();
            $cart_count = getCartCount();
            
            // Include header
            require_once __DIR__ . '/../../templates/header.php';
            
            // Include cart view template
            require_once __DIR__ . '/templates/view.php';
            
            // Include footer
            require_once __DIR__ . '/../../templates/footer.php';
            break;
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Cart module error: " . $e->getMessage());
    
    // Show error page
    require_once __DIR__ . '/../../templates/header.php';
    echo "<div class='error-container'>";
    echo "<h2>Error</h2>";
    echo "<p>We apologize, but there was an error processing your cart request. Please try again later.</p>";
    echo "</div>";
    require_once __DIR__ . '/../../templates/footer.php';
} 