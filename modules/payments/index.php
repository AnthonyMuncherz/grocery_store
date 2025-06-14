<?php
/**
 * Payments Module - Main Entry Point
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Core includes
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/constants.php';
require_once __DIR__ . '/../cart/functions.php';
require_once __DIR__ . '/../orders/functions.php';
require_once __DIR__ . '/functions.php';

// Determine requested action (default to 'checkout')
$action = $_GET['action'] ?? 'checkout';

try {
    switch ($action) {
        case 'process':
            // Ensure POST request
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?module=payments&action=checkout');
                exit;
            }

            // Collect customer data
            $customer_data = [
                'name'    => trim($_POST['name'] ?? ''),
                'email'   => trim($_POST['email'] ?? ''),
                'phone'   => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? '')
            ];

            // Validate customer data
            $validation = validateCustomerData($customer_data);
            if (!$validation['success']) {
                $_SESSION['error_message'] = $validation['message'];
                header('Location: index.php?module=payments&action=checkout');
                exit;
            }

            // Determine payment method
            $payment_method_key = $_POST['payment_method'] ?? '';
            $payment_result    = processPayment($payment_method_key, $_POST);

            if (!$payment_result['success']) {
                $_SESSION['error_message'] = $payment_result['message'];
                header('Location: index.php?module=payments&action=checkout');
                exit;
            }

            // Create order from cart
            $order_creation = createOrderFromCart($customer_data, $payment_result['payment_method'], $payment_result['transaction_id'] ?? '');
            if (!$order_creation['success']) {
                $_SESSION['error_message'] = $order_creation['message'];
                header('Location: index.php?module=payments&action=checkout');
                exit;
            }

            // Clear cart upon successful order creation
            clearCart();

            // Redirect to success page with order ID reference
            header('Location: index.php?module=payments&action=success&id=' . $order_creation['order_id']);
            exit;

        case 'success':
            $order_id = intval($_GET['id'] ?? 0);
            $order    = $order_id ? getOrderById($order_id) : null;

            require_once __DIR__ . '/../../templates/header.php';
            require_once __DIR__ . '/templates/success.php';
            require_once __DIR__ . '/../../templates/footer.php';
            break;

        case 'checkout':
        default:
            // Ensure cart is not empty
            if (isCartEmpty()) {
                $_SESSION['error_message'] = 'Your cart is empty. Please add items before checking out.';
                header('Location: index.php?module=cart');
                exit;
            }

            // Gather cart data for summary display
            $cart_items  = getCartItems();
            $cart_total  = getCartTotal();
            $cart_count  = getCartCount();
            $banks       = getMalaysianBanks();
            $ewallets    = getMalaysianEWallets();

            // Render checkout page
            require_once __DIR__ . '/../../templates/header.php';
            require_once __DIR__ . '/templates/checkout.php';
            require_once __DIR__ . '/../../templates/footer.php';
            break;
    }
} catch (Exception $e) {
    // Log and display generic error
    logError("Payments module error: " . $e->getMessage());
    http_response_code(500);
    echo '<div class="container mx-auto px-4 py-8"><h2 class="text-2xl font-bold mb-4 text-red-600">Payment Error</h2><p>An unexpected error occurred while processing your request. Please try again later.</p></div>';
} 