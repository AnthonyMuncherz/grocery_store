<?php
/**
 * Main Entry Point
 * Malaysian Grocery Store
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/constants.php';

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Initialize database connection
try {
    $db = initializeDatabase();
} catch (Exception $e) {
    logError($e->getMessage());
    die("Application Error: Could not initialize database.");
}

// Start session
session_start();

// Basic routing
$module = isset($_GET['module']) ? validateInput($_GET['module']) : 'products';
$action = isset($_GET['action']) ? validateInput($_GET['action']) : 'list';

// Define valid modules and actions
$validModules = ['products', 'orders', 'payments', 'inventory'];
$validActions = ['list', 'view', 'add', 'edit', 'delete'];

// Validate module and action
if (!in_array($module, $validModules)) {
    $module = 'products';
}
if (!in_array($action, $validActions)) {
    $action = 'list';
}

// Load module
$modulePath = __DIR__ . "/modules/$module/index.php";
if (file_exists($modulePath)) {
    require_once $modulePath;
} else {
    // Module not found, redirect to products
    header('Location: index.php?module=products');
    exit;
} 