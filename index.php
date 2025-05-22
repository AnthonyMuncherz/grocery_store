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
$module = isset($_GET['module']) ? validateInput($_GET['module']) : 'home'; // Default module is now 'home'
$default_action = ($module === 'home') ? 'index' : 'list'; // Default action for 'home' is 'index', for others 'list'
$action = isset($_GET['action']) ? validateInput($_GET['action']) : $default_action;

// Define valid modules and actions
$validModules = ['home', 'products', 'orders', 'payments', 'inventory']; // Added 'home'
// Note: Action validation might be better handled within each module.
$validActions = ['index', 'list', 'view', 'add', 'edit', 'delete', 'update_cart', 'view_cart', 'checkout', 'process_payment', 'success', 'cancel'];

// Validate module
if (!in_array($module, $validModules)) {
    logError("Invalid module requested: $module. Defaulting to 'home'.");
    $module = 'home';
    $action = 'index'; // Reset action for home module
}
// Validate action - (optional, can be handled by module)
// if (!in_array($action, $validActions)) {
//     logDebug("Action '$action' not in predefined global list. Module '$module' will handle it or default to its own default.");
//     // $action = $default_action; // Or let the module handle it.
// }


// Load module
$modulePath = __DIR__ . "/modules/$module/index.php";

if (file_exists($modulePath)) {
    require_once $modulePath;
} else {
    // Module file not found
    logError("Module file not found for module: '$module' at path: '$modulePath'");

    // If the 'home' module itself is missing (and it was the one attempted to load)
    if ($module === 'home' && (!isset($_GET['module']) || $_GET['module'] === 'home')) {
        http_response_code(500);
        // This is a critical error, so a simple die message is appropriate.
        // For a more user-friendly page, you could create a static error HTML file.
        die("Critical Error: The main home page module is missing or inaccessible. Please contact the site administrator. Path checked: " . htmlspecialchars($modulePath));
    } else {
        // If a different, non-critical module was requested and not found, redirect to the home page.
        $_SESSION['message'] = "The requested section ('" . htmlspecialchars($module) . "') was not found. Displaying the home page instead.";
        $_SESSION['message_type'] = 'warning';
        header('Location: index.php'); // Redirects to home (module=home)
        exit;
    }
}
?>