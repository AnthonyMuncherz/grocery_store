<?php
/**
 * Payment Processing Functions - Malaysian Payment Methods
 */

/**
 * Get Malaysian banks for FPX payments
 */
function getMalaysianBanks() {
    return [
        'MBB' => 'Maybank2u',
        'CIMB' => 'CIMB Clicks',
        'PBB' => 'Public Bank PBeBank',
        'HLB' => 'Hong Leong Connect',
        'RHB' => 'RHB Now',
        'AMB' => 'AmOnline',
        'UOB' => 'UOB Personal Internet Banking',
        'BSN' => 'BSN Internet Banking',
        'OCBC' => 'OCBC OnlineBanking',
        'SCB' => 'Standard Chartered Online Banking'
    ];
}

/**
 * Get Malaysian e-wallet providers
 */
function getMalaysianEWallets() {
    return [
        'TNG' => 'Touch \'n Go eWallet',
        'BOOST' => 'Boost',
        'GRABPAY' => 'GrabPay',
        'MAE' => 'MAE by Maybank',
        'SHOPEEPAY' => 'ShopeePay'
    ];
}

/**
 * Validate customer data
 */
function validateCustomerData($data) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors[] = 'Customer name is required.';
    }
    
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email address is required.';
    }
    
    if (empty($data['phone'])) {
        $errors[] = 'Phone number is required.';
    } elseif (!preg_match('/^(\+?6?01)[0-9]{8,9}$/', str_replace([' ', '-'], '', $data['phone']))) {
        $errors[] = 'Please enter a valid Malaysian phone number.';
    }
    
    if (empty($data['address'])) {
        $errors[] = 'Delivery address is required.';
    }
    
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode(' ', $errors)];
    }
    
    return ['success' => true, 'message' => 'Customer data is valid.'];
}

/**
 * Process payment based on method
 */
function processPayment($method, $data) {
    switch ($method) {
        case 'fpx':
            return processFPXPayment($data);
        case 'ewallet':
            return processEWalletPayment($data);
        case 'card':
            return processCardPayment($data);
        default:
            return ['success' => false, 'message' => 'Invalid payment method selected.'];
    }
}

/**
 * Process FPX (Online Banking) payment
 */
function processFPXPayment($data) {
    $bank_code = $data['fpx_bank'] ?? '';
    $banks = getMalaysianBanks();
    
    if (!isset($banks[$bank_code])) {
        return ['success' => false, 'message' => 'Invalid bank selected.'];
    }
    
    // Simulate payment processing
    $success_rate = 90; // 90% success rate for simulation
    $is_successful = (rand(1, 100) <= $success_rate);
    
    if ($is_successful) {
        $transaction_id = 'FPX' . date('YmdHis') . rand(1000, 9999);
        
        // Log payment attempt
        logPayment("FPX payment successful", [
            'bank' => $bank_code,
            'transaction_id' => $transaction_id,
            'amount' => getCartTotal()
        ]);
        
        return [
            'success' => true,
            'message' => 'Payment successful via ' . $banks[$bank_code],
            'transaction_id' => $transaction_id,
            'payment_method' => 'FPX - ' . $banks[$bank_code]
        ];
    } else {
        // Simulate various failure reasons
        $failure_reasons = [
            'Insufficient funds in your account',
            'Bank system temporarily unavailable',
            'Transaction limit exceeded',
            'Invalid bank credentials'
        ];
        
        $failure_message = $failure_reasons[array_rand($failure_reasons)];
        
        logPayment("FPX payment failed", [
            'bank' => $bank_code,
            'reason' => $failure_message,
            'amount' => getCartTotal()
        ]);
        
        return ['success' => false, 'message' => $failure_message];
    }
}

/**
 * Process e-wallet payment
 */
function processEWalletPayment($data) {
    $ewallet_provider = $data['ewallet_provider'] ?? '';
    $phone_number = $data['ewallet_phone'] ?? '';
    
    $providers = getMalaysianEWallets();
    
    if (!isset($providers[$ewallet_provider])) {
        return ['success' => false, 'message' => 'Invalid e-wallet provider selected.'];
    }
    
    if (empty($phone_number)) {
        return ['success' => false, 'message' => 'Phone number is required for e-wallet payment.'];
    }
    
    // Validate Malaysian phone number
    if (!preg_match('/^(\+?6?01)[0-9]{8,9}$/', str_replace([' ', '-'], '', $phone_number))) {
        return ['success' => false, 'message' => 'Please enter a valid Malaysian phone number.'];
    }
    
    // Simulate payment processing
    $success_rate = 85; // 85% success rate for e-wallet
    $is_successful = (rand(1, 100) <= $success_rate);
    
    if ($is_successful) {
        $transaction_id = strtoupper($ewallet_provider) . date('YmdHis') . rand(1000, 9999);
        
        logPayment("E-wallet payment successful", [
            'provider' => $ewallet_provider,
            'phone' => $phone_number,
            'transaction_id' => $transaction_id,
            'amount' => getCartTotal()
        ]);
        
        return [
            'success' => true,
            'message' => 'Payment successful via ' . $providers[$ewallet_provider],
            'transaction_id' => $transaction_id,
            'payment_method' => $providers[$ewallet_provider]
        ];
    } else {
        $failure_reasons = [
            'Insufficient balance in your e-wallet',
            'E-wallet service temporarily unavailable',
            'Daily transaction limit exceeded',
            'Phone number not registered with e-wallet'
        ];
        
        $failure_message = $failure_reasons[array_rand($failure_reasons)];
        
        logPayment("E-wallet payment failed", [
            'provider' => $ewallet_provider,
            'phone' => $phone_number,
            'reason' => $failure_message,
            'amount' => getCartTotal()
        ]);
        
        return ['success' => false, 'message' => $failure_message];
    }
}

/**
 * Process credit/debit card payment
 */
function processCardPayment($data) {
    $card_number = str_replace(' ', '', $data['card_number'] ?? '');
    $expiry_month = $data['expiry_month'] ?? '';
    $expiry_year = $data['expiry_year'] ?? '';
    $cvv = $data['cvv'] ?? '';
    $card_holder = $data['card_holder'] ?? '';
    
    // Validate card data
    if (empty($card_number) || !validateCardNumber($card_number)) {
        return ['success' => false, 'message' => 'Invalid card number.'];
    }
    
    if (empty($expiry_month) || empty($expiry_year)) {
        return ['success' => false, 'message' => 'Card expiry date is required.'];
    }
    
    if (empty($cvv) || !preg_match('/^\d{3,4}$/', $cvv)) {
        return ['success' => false, 'message' => 'Invalid CVV code.'];
    }
    
    if (empty($card_holder)) {
        return ['success' => false, 'message' => 'Card holder name is required.'];
    }
    
    // Check if card is expired
    $current_year = intval(date('Y'));
    $current_month = intval(date('m'));
    $exp_year = intval('20' . $expiry_year);
    $exp_month = intval($expiry_month);
    
    if ($exp_year < $current_year || ($exp_year == $current_year && $exp_month < $current_month)) {
        return ['success' => false, 'message' => 'Card has expired.'];
    }
    
    // Simulate payment processing
    $success_rate = 92; // 92% success rate for cards
    $is_successful = (rand(1, 100) <= $success_rate);
    
    if ($is_successful) {
        $transaction_id = 'CARD' . date('YmdHis') . rand(1000, 9999);
        $card_type = getCardType($card_number);
        
        logPayment("Card payment successful", [
            'card_type' => $card_type,
            'card_last4' => substr($card_number, -4),
            'transaction_id' => $transaction_id,
            'amount' => getCartTotal()
        ]);
        
        return [
            'success' => true,
            'message' => 'Payment successful via ' . $card_type,
            'transaction_id' => $transaction_id,
            'payment_method' => $card_type . ' ending in ' . substr($card_number, -4)
        ];
    } else {
        $failure_reasons = [
            'Card declined by bank',
            'Insufficient funds',
            'Card limit exceeded',
            'Transaction not authorized'
        ];
        
        $failure_message = $failure_reasons[array_rand($failure_reasons)];
        
        logPayment("Card payment failed", [
            'card_last4' => substr($card_number, -4),
            'reason' => $failure_message,
            'amount' => getCartTotal()
        ]);
        
        return ['success' => false, 'message' => $failure_message];
    }
}

/**
 * Validate card number using Luhn algorithm
 */
function validateCardNumber($number) {
    $number = preg_replace('/\D/', '', $number);
    $length = strlen($number);
    
    if ($length < 13 || $length > 19) {
        return false;
    }
    
    $sum = 0;
    $reverse = strrev($number);
    
    for ($i = 0; $i < $length; $i++) {
        $digit = $reverse[$i];
        if ($i % 2 == 1) {
            $digit *= 2;
            if ($digit > 9) {
                $digit = $digit - 9;
            }
        }
        $sum += $digit;
    }
    
    return $sum % 10 == 0;
}

/**
 * Get card type from card number
 */
function getCardType($number) {
    $number = preg_replace('/\D/', '', $number);
    
    if (preg_match('/^4/', $number)) {
        return 'Visa';
    } elseif (preg_match('/^5[1-5]/', $number)) {
        return 'Mastercard';
    } elseif (preg_match('/^3[47]/', $number)) {
        return 'American Express';
    } else {
        return 'Credit Card';
    }
}

/**
 * Create order from cart
 */
function createOrderFromCart($customer_data, $payment_method, $transaction_id) {
    try {
        $cart_items = getCartItems();
        $cart_total = getCartTotal();
        
        if (empty($cart_items)) {
            return ['success' => false, 'message' => 'Cart is empty.'];
        }
        
        // Prepare order items for the createOrder function
        $order_items = [];
        foreach ($cart_items as $item) {
            $order_items[] = [
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price']
            ];
        }
        
        // Create the order using the existing function
        $order_id = createOrder(
            $customer_data['name'],
            $customer_data['email'],
            $customer_data['phone'],
            $customer_data['address'],
            $cart_total,
            'CONFIRMED',
            $payment_method,
            $order_items
        );
        
        if ($order_id) {
            return [
                'success' => true,
                'message' => 'Order created successfully.',
                'order_id' => $order_id
            ];
        } else {
            return ['success' => false, 'message' => 'Failed to create order.'];
        }
        
    } catch (Exception $e) {
        error_log("Create order error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error creating order: ' . $e->getMessage()];
    }
}

/**
 * Get order by ID (defined here only if Orders module hasn't loaded it)
 */
if (!function_exists('getOrderById')) {
function getOrderById($order_id) {
    try {
        $db = getDbConnection();
        
        $stmt = $db->prepare("
            SELECT o.*, 
                   GROUP_CONCAT(oi.product_id || ':' || oi.quantity || ':' || oi.unit_price, '|') as items
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.id = :id
            GROUP BY o.id
        ");
        $stmt->bindValue(':id', $order_id, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        $order = $result->fetchArray(SQLITE3_ASSOC);
        
        if ($order) {
            // Parse order items
            $order['order_items'] = [];
            if (!empty($order['items'])) {
                $items = explode('|', $order['items']);
                foreach ($items as $item) {
                    $parts = explode(':', $item);
                    if (count($parts) == 3) {
                        $order['order_items'][] = [
                            'product_id' => $parts[0],
                            'quantity' => $parts[1],
                            'unit_price' => $parts[2]
                        ];
                    }
                }
            }
            unset($order['items']);
        }
        
        return $order;
        
    } catch (Exception $e) {
        error_log("Get order error: " . $e->getMessage());
        return null;
    }
}
} // end conditional 