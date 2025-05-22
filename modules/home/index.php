<?php
/**
 * Home Module - Main Entry Point
 */

// Include necessary files
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/constants.php';
// If you need to fetch categories or products for the home page, include product functions:
// require_once __DIR__ . '/../../modules/products/functions.php';

// Initialize variables for the template (example: featured categories/products)
// $featuredCategories = []; // Example: getCategories(null, null, 3); // Get 3 categories
// $featuredProducts = []; // Example: getProducts(null, null, 4); // Get 4 products

// Action handling for home module (if any future actions are needed)
// $current_action = isset($_GET['action']) ? validateInput($_GET['action']) : 'index';

try {
    // Include header
    require_once __DIR__ . '/../../templates/header.php';

    // Include home page template
    require_once __DIR__ . '/templates/main.php';

    // Include footer
    require_once __DIR__ . '/../../templates/footer.php';

} catch (Exception $e) {
    // Log error
    error_log("Home module error: " . $e->getMessage());

    // Show error page
    require_once __DIR__ . '/../../templates/header.php'; // Ensure header is included for consistent layout
    echo "<div class='container mx-auto p-6 text-center'>";
    echo "<h2 class='text-2xl font-semibold text-red-600 mb-4'>Oops! Something went wrong.</h2>";
    echo "<p class='text-gray-700'>We apologize, but there was an error displaying the home page. Please try again later or contact support if the issue persists.</p>";
    // echo "<p class='text-sm text-gray-500 mt-2'>Error details: " . htmlspecialchars($e->getMessage()) . "</p>"; // For debugging, remove for production
    echo "</div>";
    require_once __DIR__ . '/../../templates/footer.php'; // Ensure footer is included
}
?>