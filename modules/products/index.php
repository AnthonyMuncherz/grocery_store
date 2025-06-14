<?php
/**
 * Products Module - Main Entry Point
 */

// Include necessary files
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/constants.php';
require_once __DIR__ . '/functions.php';

// Get action parameter
$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'view':
            // Get product ID
            $product_id = intval($_GET['id']);
            if ($product_id <= 0) {
                throw new Exception("Invalid product ID");
            }
            
            // Get product details
            $product = getProduct($product_id);
            if (!$product) {
                throw new Exception("Product not found");
            }
            
            // Get related products from same category
            $related_products = getRelatedProducts($product['category_id'], $product_id, 4);
            
            // Include header
            require_once __DIR__ . '/../../templates/header.php';
            
            // Include product view template
            require_once __DIR__ . '/templates/view.php';
            
            // Include footer
            require_once __DIR__ . '/../../templates/footer.php';
            break;
            
        case 'list':
        default:
            // Initialize variables
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $items_per_page = 12;
            $offset = ($page - 1) * $items_per_page;

            // Get filter parameters
            $category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
            $search = isset($_GET['search']) ? trim($_GET['search']) : null;

            // Get total products count for pagination
            $total_products = getProductCount($category_id, $search);
            $total_pages = ceil($total_products / $items_per_page);
            
            // Ensure current page is within valid range
            $current_page = min($page, $total_pages);
            
            // Get products for current page
            $products = getProducts($category_id, $search, $items_per_page, $offset);
            
            // Get all categories for filter dropdown
            $categories = getCategories();
            
            // Include header
            require_once __DIR__ . '/../../templates/header.php';
            
            // Include products listing template
            require_once __DIR__ . '/templates/list.php';
            
            // Include footer
            require_once __DIR__ . '/../../templates/footer.php';
            break;
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Products module error: " . $e->getMessage());
    
    // Show error page
    require_once __DIR__ . '/../../templates/header.php';
    echo "<div class='error-container'>";
    echo "<h2>Error</h2>";
    echo "<p>We apologize, but there was an error processing your request. Please try again later.</p>";
    echo "</div>";
    require_once __DIR__ . '/../../templates/footer.php';
} 