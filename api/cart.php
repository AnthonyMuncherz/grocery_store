<?php
/**
 * Cart API Endpoint
 * Handles AJAX cart operations
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../modules/cart/functions.php';

// Set JSON content type
header('Content-Type: application/json');

// Get action parameter
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Initialize response
$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($action) {
        case 'add':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $product_id = intval($_POST['product_id']);
                $quantity = intval($_POST['quantity']) ?: 1;
                
                if ($product_id > 0) {
                    $result = addToCart($product_id, $quantity);
                    $response = $result;
                    
                    if ($result['success']) {
                        $response['data'] = [
                            'cart_count' => getCartCount(),
                            'cart_total' => getCartTotal()
                        ];
                    }
                } else {
                    $response['message'] = 'Invalid product ID.';
                }
            } else {
                $response['message'] = 'Invalid request method.';
            }
            break;
            
        case 'remove':
            $product_id = intval($_GET['product_id']) ?: intval($_POST['product_id']);
            if ($product_id > 0) {
                $success = removeFromCart($product_id);
                $response['success'] = $success;
                $response['message'] = $success ? 'Product removed from cart.' : 'Error removing product.';
                
                if ($success) {
                    $response['data'] = [
                        'cart_count' => getCartCount(),
                        'cart_total' => getCartTotal()
                    ];
                }
            } else {
                $response['message'] = 'Invalid product ID.';
            }
            break;
            
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $product_id = intval($_POST['product_id']);
                $quantity = intval($_POST['quantity']);
                
                if ($product_id > 0 && $quantity > 0) {
                    $success = updateCartItem($product_id, $quantity);
                    $response['success'] = $success;
                    $response['message'] = $success ? 'Cart updated successfully.' : 'Error updating cart.';
                    
                    if ($success) {
                        $response['data'] = [
                            'cart_count' => getCartCount(),
                            'cart_total' => getCartTotal()
                        ];
                    }
                } else {
                    $response['message'] = 'Invalid product ID or quantity.';
                }
            } else {
                $response['message'] = 'Invalid request method.';
            }
            break;
            
        case 'clear':
            $success = clearCart();
            $response['success'] = $success;
            $response['message'] = $success ? 'Cart cleared successfully.' : 'Error clearing cart.';
            
            if ($success) {
                $response['data'] = [
                    'cart_count' => 0,
                    'cart_total' => 0.00
                ];
            }
            break;
            
        case 'get':
            $cart_items = getCartItems();
            $response['success'] = true;
            $response['message'] = 'Cart retrieved successfully.';
            $response['data'] = [
                'items' => $cart_items,
                'count' => getCartCount(),
                'total' => getCartTotal(),
                'is_empty' => isCartEmpty()
            ];
            break;
            
        case 'count':
            $response['success'] = true;
            $response['message'] = 'Cart count retrieved successfully.';
            $response['data'] = ['count' => getCartCount()];
            // For backward compatibility
            $response['count'] = getCartCount();
            break;
            
        case 'total':
            $response['success'] = true;
            $response['message'] = 'Cart total retrieved successfully.';
            $response['data'] = ['total' => getCartTotal()];
            break;
            
        default:
            $response['message'] = 'Invalid action specified.';
    }
    
} catch (Exception $e) {
    error_log("Cart API error: " . $e->getMessage());
    $response = [
        'success' => false,
        'message' => 'An error occurred while processing your request.',
        'data' => null
    ];
}

// Return JSON response
echo json_encode($response);
exit; 