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
            $_SESSION['success_message'] = "Product added successfully!";
            header("Location: index.php?module=inventory&action=list");
            exit;
        } else {
            $error = "Failed to add product, please try again.";
        }
    }
}

// Handle stock update from edit page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_stock') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $adjustment = intval($_POST['adjustment'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    
    $error = null;
    
    // Validation
    if ($product_id <= 0) {
        $error = "Invalid product ID.";
    } elseif ($adjustment === 0) {
        $error = "Adjustment quantity cannot be zero.";
    } else {
        // Use the existing updateProductStock function
        $success = updateProductStock($product_id, $adjustment, $notes);
        if ($success) {
            $_SESSION['success_message'] = "Stock updated successfully! Adjustment: " . ($adjustment > 0 ? "+$adjustment" : "$adjustment") . " units.";
        } else {
            $_SESSION['error_message'] = "Failed to update stock. Please check if the adjustment would result in negative stock.";
        }
    }
    
    // Redirect back to edit page
    header("Location: index.php?module=inventory&action=edit&id=$product_id");
    exit;
}

// Handle soft delete from edit page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'soft_delete') {
    $product_id = intval($_POST['product_id'] ?? 0);
    
    if ($product_id > 0) {
        try {
            $db = getDbConnection();
            $stmt = $db->prepare("UPDATE products SET deleted_at = datetime('now') WHERE id = :id");
            $stmt->bindValue(':id', $product_id, SQLITE3_INTEGER);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Product has been hidden successfully.";
            } else {
                $_SESSION['error_message'] = "Failed to hide product.";
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error: " . $e->getMessage();
            logError("Soft delete failed for product $product_id: " . $e->getMessage());
        }
    } else {
        $_SESSION['error_message'] = "Invalid product ID.";
    }
    
    header("Location: index.php?module=inventory&action=list");
    exit;
}

// Handle hard delete from edit page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'hard_delete') {
    $product_id = intval($_POST['product_id'] ?? 0);
    
    if ($product_id > 0) {
        try {
            $db = getDbConnection();
            
            // Start transaction
            $db->exec('BEGIN TRANSACTION');
            
            // Delete inventory logs first (foreign key constraint)
            $stmt = $db->prepare("DELETE FROM inventory_logs WHERE product_id = :id");
            $stmt->bindValue(':id', $product_id, SQLITE3_INTEGER);
            $stmt->execute();
            
            // Delete the product
            $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
            $stmt->bindValue(':id', $product_id, SQLITE3_INTEGER);
            
            if ($stmt->execute()) {
                $db->exec('COMMIT');
                $_SESSION['success_message'] = "Product has been permanently deleted.";
            } else {
                $db->exec('ROLLBACK');
                $_SESSION['error_message'] = "Failed to delete product.";
            }
        } catch (Exception $e) {
            $db->exec('ROLLBACK');
            $_SESSION['error_message'] = "Error: " . $e->getMessage();
            logError("Hard delete failed for product $product_id: " . $e->getMessage());
        }
    } else {
        $_SESSION['error_message'] = "Invalid product ID.";
    }
    
    header("Location: index.php?module=inventory&action=list");
    exit;
}

// Handle bulk stock update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'bulk_stock_update') {
    $updates = $_POST['stock_updates'] ?? [];
    $success_count = 0;
    $errors = [];
    
    foreach ($updates as $product_id => $data) {
        $adjustment = intval($data['adjustment'] ?? 0);
        $note = trim($data['note'] ?? '');
        
        if ($adjustment !== 0) {
            $result = updateProductStock($product_id, $adjustment, $note);
            if ($result) {
                $success_count++;
            } else {
                $errors[] = "Failed to update stock for product ID: $product_id";
            }
        }
    }
    
    if ($success_count > 0) {
        $_SESSION['success_message'] = "Successfully updated stock for $success_count product(s).";
    }
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode(', ', $errors);
    }
    
    header("Location: index.php?module=inventory&action=list");
    exit;
}

require_once __DIR__ . '/../../templates/header.php';

// Display session messages
if (isset($_SESSION['success_message'])) {
    echo '<div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">';
    echo '<i class="fas fa-check-circle mr-2"></i>' . htmlspecialchars($_SESSION['success_message']);
    echo '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">';
    echo '<i class="fas fa-exclamation-circle mr-2"></i>' . htmlspecialchars($_SESSION['error_message']);
    echo '</div>';
    unset($_SESSION['error_message']);
}

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
    case 'bulk_update':
        require_once __DIR__ . '/templates/bulk_update.php';
        break;
    default:
        echo '<div class="text-center py-12">';
        echo '<i class="fas fa-exclamation-triangle text-6xl text-gray-400 mb-4"></i>';
        echo '<h2 class="text-2xl font-bold text-gray-700 mb-2">Unknown Action</h2>';
        echo '<p class="text-gray-600 mb-6">The requested action "' . htmlspecialchars($action) . '" is not recognized.</p>';
        echo '<a href="index.php?module=inventory" class="bg-theme-red hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-md">';
        echo '<i class="fas fa-arrow-left mr-2"></i>Back to Inventory';
        echo '</a>';
        echo '</div>';
        break;
}

require_once __DIR__ . '/../../templates/footer.php';
