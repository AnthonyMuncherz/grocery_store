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
    logError("Database initialization failed: " . $e->getMessage());
    die("Application Error: Could not initialize database. Please check logs.");
}

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Basic routing
$module = isset($_GET['module']) ? validateInput($_GET['module']) : 'products';
$action = isset($_GET['action']) ? validateInput($_GET['action']) : 'list';

// Define valid modules and actions
$validModules = ['products', 'orders', 'payments', 'inventory'];
// Note: Action validation might be better handled within each module,
// as actions can vary significantly between modules.
// For now, this basic list is kept as per original structure.
$validActions = ['list', 'view', 'add', 'edit', 'delete', 'update_cart', 'view_cart', 'checkout', 'process_payment', 'success', 'cancel']; // Added more common actions

// Validate module
if (!in_array($module, $validModules)) {
    logError("Invalid module requested: $module. Defaulting to 'products'.");
    $module = 'products';
}
// Validate action - it might be better to let modules handle their own action validation
if (!in_array($action, $validActions)) {
    // If action is not in a generic list, we assume it might be valid for the specific module
    // or it defaults to 'list'. For now, let's keep the default to 'list' if unsure.
    // A more robust approach would be for each module to declare its valid actions.
    // logDebug("Action '$action' not in predefined global list. Module '$module' will handle it or default.");
    // For now, to maintain original behavior if action is not "known globally":
    // $action = 'list'; // Or remove this line if modules handle unknown actions gracefully
}


// Load module
$modulePath = __DIR__ . "/modules/$module/index.php";

if (file_exists($modulePath)) {
    require_once $modulePath;
} else {
    // Module file not found
    logError("Module file not found for module: '$module' at path: '$modulePath'");

    if ($module === 'products' && (!isset($_GET['module']) || $_GET['module'] === 'products')) {
        // If the request was for 'products' (either by default or explicitly)
        // and its file is missing, this is a critical error. Stop to prevent looping.
        http_response_code(500); // Internal Server Error
        die("Critical Error: The main product module is missing or inaccessible. Please contact the site administrator. Path checked: " . htmlspecialchars($modulePath));
    } else {
        // If a different module was requested and not found, try redirecting to the default 'products' module.
        // This assumes 'products' module should normally exist.
        $_SESSION['message'] = "The requested section ('" . htmlspecialchars($module) . "') was not found. Displaying products instead.";
        $_SESSION['message_type'] = 'warning';
        header('Location: index.php?module=products');
        exit;
    }
}
?>