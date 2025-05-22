<?php
/**
 * Common Utility Functions
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/constants.php';

/**
 * Input validation
 */
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Format currency in MYR
 */
function formatMYR($amount) {
    return CURRENCY_SYMBOL . ' ' . number_format($amount, 2);
}

/**
 * Format Malaysian phone number
 */
function formatMYPhone($phone) {
    // Malaysian format: +60 XX-XXXXXXXX
    return preg_replace('/(\d{2})(\d{8})/', '+60 $1-$2', $phone);
}

/**
 * Generate unique reference number
 */
function generateReference($prefix = 'REF') {
    return $prefix . date('YmdHis') . rand(1000, 9999);
}

/**
 * Error logging
 */
function logError($message, $type = 'ERROR', $logFile = ERROR_LOG) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$type] $message" . PHP_EOL;
    error_log($logMessage, 3, $logFile);
}

/**
 * Debug logging
 */
function logDebug($message, $data = null) {
    if ($data) {
        $message .= ' Data: ' . json_encode($data);
    }
    logError($message, 'DEBUG', DEBUG_LOG);
}

/**
 * Payment logging
 */
function logPayment($message, $data = null) {
    if ($data) {
        $message .= ' Data: ' . json_encode($data);
    }
    logError($message, 'PAYMENT', PAYMENT_LOG);
}

/**
 * Validate image upload
 */
function validateImage($file) {
    $errors = [];
    
    if ($file['size'] > MAX_IMAGE_SIZE) {
        $errors[] = 'File size exceeds limit of ' . (MAX_IMAGE_SIZE / 1024 / 1024) . 'MB';
    }
    
    if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
        $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', array_map(function($type) {
            return str_replace('image/', '', $type);
        }, ALLOWED_IMAGE_TYPES));
    }
    
    return $errors;
}

/**
 * Generate success response
 */
function successResponse($data = null, $message = 'Success') {
    return json_encode([
        'status' => 'success',
        'message' => $message,
        'data' => $data
    ]);
}

/**
 * Generate error response
 */
function errorResponse($message = 'Error', $code = 400) {
    return json_encode([
        'status' => 'error',
        'message' => $message,
        'code' => $code
    ]);
}

/**
 * Set response headers
 */
function setResponseHeaders($contentType = 'application/json') {
    header("Content-Type: $contentType");
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
} 