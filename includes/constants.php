<?php
/**
 * Application Constants
 */

// Application settings
define('APP_NAME', 'Malaysian Grocery Store');
define('APP_VERSION', '1.0.0');
define('APP_TIMEZONE', 'Asia/Kuala_Lumpur');

// Currency settings
define('CURRENCY', 'MYR');
define('CURRENCY_SYMBOL', 'RM');

// Order status
define('ORDER_STATUS_PENDING', 'PENDING');
define('ORDER_STATUS_PROCESSING', 'PROCESSING');
define('ORDER_STATUS_COMPLETED', 'COMPLETED');
define('ORDER_STATUS_CANCELLED', 'CANCELLED');
define('ORDER_STATUS_REFUNDED', 'REFUNDED');

// Payment methods
define('PAYMENT_METHOD_FPX', 'FPX');
define('PAYMENT_METHOD_CARD', 'CARD');
define('PAYMENT_METHOD_EWALLET', 'EWALLET');

// Payment status
define('PAYMENT_STATUS_PENDING', 'PENDING');
define('PAYMENT_STATUS_PROCESSING', 'PROCESSING');
define('PAYMENT_STATUS_COMPLETED', 'COMPLETED');
define('PAYMENT_STATUS_FAILED', 'FAILED');
define('PAYMENT_STATUS_REFUNDED', 'REFUNDED');

// Inventory actions
define('INVENTORY_ACTION_IN', 'STOCK_IN');
define('INVENTORY_ACTION_OUT', 'STOCK_OUT');
define('INVENTORY_ACTION_ADJUST', 'STOCK_ADJUST');

// Error logging
define('LOG_PATH', __DIR__ . '/../logs/');
define('ERROR_LOG', LOG_PATH . 'error.log');
define('PAYMENT_LOG', LOG_PATH . 'payment.log');
define('DEBUG_LOG', LOG_PATH . 'debug.log');

// Image settings
define('PRODUCT_IMAGE_PATH', __DIR__ . '/../assets/images/products/');
define('MAX_IMAGE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']); 