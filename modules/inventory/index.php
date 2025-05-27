<?php
require_once __DIR__ . '/../../includes/functions.php'; // for getDbConnection()
require_once __DIR__ . '/functions.php';                // for inventory functions

$action = $_GET['action'] ?? 'list';

// Handle POST add_product BEFORE any output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_product') {
    // Sanitize and get inputs
    $product_id = trim($_POST['product_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $stock_quantity = intval($_POST['stock_quantity'] ?? 0);

    $error = null;

    // Validation
    if (!$name) {
        $error = "Product name is required.";
    } elseif ($price <= 0) {
        $error = "Price must be greater than zero.";
    } elseif ($category_id <= 0) {
        $error = "Please select a category.";
    } elseif ($stock_quantity < 0) {
        $error = "Stock quantity cannot be negative.";
    }

    if (!$error) {
        // Call addProduct function
        $success = addProduct($product_id ?: null, $name, $description, $price, $category_id, $stock_quantity);
        if ($success) {
            header("Location: index.php?module=inventory&action=list");
            exit;
        } else {
            $error = "Failed to add product, please try again.";
        }
    }
}

// Now output headers and content
require_once __DIR__ . '/../../templates/header.php';

switch ($action) {
    case 'list':
        require_once __DIR__ . '/templates/list.php';
        break;
    case 'view':
        require_once __DIR__ . '/templates/view.php';
        break;
    case 'add':
        require_once __DIR__ . '/templates/add.php';
        break;
    case 'edit':
        require_once __DIR__ . '/templates/edit.php';
        break;
    default:
        echo "Unknown action: " . htmlspecialchars($action);
        break;
}

// Include footer at the end
require_once __DIR__ . '/../../templates/footer.php';
