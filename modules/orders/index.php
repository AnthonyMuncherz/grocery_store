<?php
/**
 * Orders Module - Main Entry Point
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/constants.php';
require_once __DIR__ . '/functions.php';  // orders module functions

// Get action parameter
$action = isset($_GET['action']) ? validateInput($_GET['action']) : 'list';

// Handle different actions
switch ($action) {
    case 'list':
        // Get filter parameters
        $status_filter = isset($_GET['status']) ? validateInput($_GET['status']) : '';
        $sort_by = isset($_GET['sort_by']) ? validateInput($_GET['sort_by']) : 'created_at';
        $sort_dir = isset($_GET['sort_dir']) ? validateInput($_GET['sort_dir']) : 'DESC';
        
        // Pagination parameters
        $per_page = 20;
        $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($current_page - 1) * $per_page;
        
        try {
            // Get orders based on filters
            $orders = getAllOrders($per_page, $offset, $sort_by, $sort_dir);
            $total_orders = getTotalOrdersCount();
            $total_pages = ceil($total_orders / $per_page);
            
            // Include header and list template
            require_once __DIR__ . '/../../templates/header.php';
            require_once __DIR__ . '/templates/list.php';
            require_once __DIR__ . '/../../templates/footer.php';
            
        } catch (Exception $e) {
            $error = "Error loading orders: " . $e->getMessage();
            require_once __DIR__ . '/../../templates/header.php';
            echo "<div class='alert alert-danger'>$error</div>";
            require_once __DIR__ . '/../../templates/footer.php';
        }
        break;
        
    case 'view':
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($order_id <= 0) {
            header('Location: index.php?module=orders&action=list');
            exit;
        }
        
        try {
            $order = getOrderById($order_id);
            if (!$order) {
                $_SESSION['message'] = "Order not found.";
                $_SESSION['message_type'] = 'error';
                header('Location: index.php?module=orders&action=list');
                exit;
            }
            
            // Include view template (to be created)
            require_once __DIR__ . '/../../templates/header.php';
            require_once __DIR__ . '/templates/view.php';
            require_once __DIR__ . '/../../templates/footer.php';
            
        } catch (Exception $e) {
            $_SESSION['message'] = "Error loading order: " . $e->getMessage();
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?module=orders&action=list');
            exit;
        }
        break;
        
    case 'edit':
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($order_id <= 0) {
            $_SESSION['message'] = "Invalid order ID.";
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?module=orders&action=list');
            exit;
        }
        
        try {
            $order = getOrderById($order_id);
            if (!$order) {
                $_SESSION['message'] = "Order not found.";
                $_SESSION['message_type'] = 'error';
                header('Location: index.php?module=orders&action=list');
                exit;
            }
            
            $products = getProducts();
            
            // Include edit template
            require_once __DIR__ . '/../../templates/header.php';
            require_once __DIR__ . '/templates/edit.php';
            require_once __DIR__ . '/../../templates/footer.php';
            
        } catch (Exception $e) {
            $_SESSION['message'] = "Error loading order: " . $e->getMessage();
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?module=orders&action=list');
            exit;
        }
        break;
        
    case 'update':
        $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        
        if ($order_id <= 0) {
            $_SESSION['message'] = "Invalid order ID.";
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?module=orders&action=list');
            exit;
        }
        
        try {
            // Check if order exists
            if (!orderExists($order_id)) {
                $_SESSION['message'] = "Order not found.";
                $_SESSION['message_type'] = 'error';
                header('Location: index.php?module=orders&action=list');
                exit;
            }
            
            handleUpdateOrder($order_id);
            
        } catch (Exception $e) {
            $_SESSION['message'] = "Error updating order: " . $e->getMessage();
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?module=orders&action=edit&id=' . $order_id);
            exit;
        }
        break;
        
    case 'delete':
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($order_id <= 0) {
            $_SESSION['message'] = "Invalid order ID.";
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?module=orders&action=list');
            exit;
        }
        
        try {
            if (!orderExists($order_id)) {
                $_SESSION['message'] = "Order not found.";
                $_SESSION['message_type'] = 'error';
                header('Location: index.php?module=orders&action=list');
                exit;
            }
            
            $result = deleteOrder($order_id);
            
            if ($result) {
                $_SESSION['message'] = "Order #$order_id has been successfully deleted.";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Failed to delete order.";
                $_SESSION['message_type'] = 'error';
            }
            
        } catch (Exception $e) {
            $_SESSION['message'] = "Error deleting order: " . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }
        
        header('Location: index.php?module=orders&action=list');
        exit;
        break;
        
    case 'add':
    default:
        // Default behavior - create order (existing code)
        handleCreateOrder();
        break;
}

function handleUpdateOrder($order_id) {
    global $db;
    
    $errors = [];
    $success = '';
    
    // Sanitize and validate input
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? '');
    $status = trim($_POST['status'] ?? 'pending');
    
    $product_ids = $_POST['product_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    if ($customer_name === '') $errors[] = "Customer name is required.";
    if ($customer_email === '' || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if ($customer_phone === '') $errors[] = "Customer phone is required.";
    if ($shipping_address === '') $errors[] = "Shipping address is required.";
    if ($payment_method === '') $errors[] = "Payment method is required.";

    // Validate order items
    $order_items = [];
    $products = getProducts();
    
    for ($i = 0; $i < count($product_ids); $i++) {
        $pid = intval($product_ids[$i]);
        $qty = intval($quantities[$i]);
        if ($pid <= 0 || $qty <= 0) continue;

        // Check product exists & stock
        $product = null;
        foreach ($products as $p) {
            if ($p['id'] == $pid) {
                $product = $p;
                break;
            }
        }
        if (!$product) {
            $errors[] = "Selected product does not exist.";
            break;
        }

        $order_items[] = [
            'product_id' => $pid,
            'quantity' => $qty,
            'unit_price' => $product['price'],
            'subtotal' => $product['price'] * $qty,
        ];
    }

    if (count($order_items) === 0) {
        $errors[] = "At least one valid order item is required.";
    }

    // Check for duplicate products
    $product_ids_checked = array_column($order_items, 'product_id');
    if (count($product_ids_checked) !== count(array_unique($product_ids_checked))) {
        $errors[] = "Duplicate products are not allowed in the order.";
    }

    if (empty($errors)) {
        // Calculate total amount
        $total_amount = 0;
        foreach ($order_items as $item) {
            $total_amount += $item['subtotal'];
        }

        // Update order
        $result = updateOrder(
            $order_id,
            $customer_name,
            $customer_email,
            $customer_phone,
            $shipping_address,
            $total_amount,
            $status,
            $payment_method,
            $order_items
        );

        if ($result) {
            $_SESSION['message'] = "Order #$order_id updated successfully. Total: RM " . number_format($total_amount, 2);
            $_SESSION['message_type'] = 'success';
            header('Location: index.php?module=orders&action=view&id=' . $order_id);
            exit;
        } else {
            $errors[] = "Failed to update order.";
        }
    }
    
    // If there are errors, redirect back to edit form with errors
    $_SESSION['edit_errors'] = $errors;
    header('Location: index.php?module=orders&action=edit&id=' . $order_id);
    exit;
}

function handleCreateOrder() {
    global $db;
    
    $errors = [];
    $success = '';

    try {
        // Fetch all products to list in dropdown
        $products = getProducts();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate input
        $customer_name = trim($_POST['customer_name'] ?? '');
        $customer_email = trim($_POST['customer_email'] ?? '');
        $customer_phone = trim($_POST['customer_phone'] ?? '');
        $shipping_address = trim($_POST['shipping_address'] ?? '');
        $payment_method = trim($_POST['payment_method'] ?? '');
        // $payment_status = trim($_POST['payment_status'] ?? '');
        
        $product_ids = $_POST['product_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];

        if ($customer_name === '') $errors[] = "Customer name is required.";
        if ($customer_email === '' || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
        if ($customer_phone === '') $errors[] = "Customer phone is required.";
        if ($shipping_address === '') $errors[] = "Shipping address is required.";
        if ($payment_method === '') $errors[] = "Payment method is required.";
        // if ($payment_status === '') $errors[] = "Payment status is required.";

        // Validate order items
        $order_items = [];
        for ($i = 0; $i < count($product_ids); $i++) {
            $pid = intval($product_ids[$i]);
            $qty = intval($quantities[$i]);
            if ($pid <= 0 || $qty <= 0) continue;

            // Check product exists & stock
            $product = null;
            foreach ($products as $p) {
                if ($p['id'] == $pid) {
                    $product = $p;
                    break;
                }
            }
            if (!$product) {
                $errors[] = "Selected product does not exist.";
                break;
            }
            if ($qty > $product['stock_quantity']) {
                $errors[] = "Not enough stock for product: {$product['name']}.";
                break;
            }

            $order_items[] = [
                'product_id' => $pid,
                'quantity' => $qty,
                'unit_price' => $product['price'],
                'subtotal' => $product['price'] * $qty,
            ];
        }

        if (count($order_items) === 0) {
            $errors[] = "At least one valid order item is required.";
        }

        // Check for duplicate products
        $product_ids_checked = array_column($order_items, 'product_id');
        if (count($product_ids_checked) !== count(array_unique($product_ids_checked))) {
            $errors[] = "Duplicate products are not allowed in the order.";
        }

        if (empty($errors)) {
            // Calculate total amount
            $total_amount = 0;
            foreach ($order_items as $item) {
                $total_amount += $item['subtotal'];
            }

            // Create order
            $order_id = createOrder(
                $customer_name,
                $customer_email,
                $customer_phone,
                $shipping_address,
                $total_amount,
                'pending',          // default status
                $payment_method,
                // $payment_status,
                $order_items
            );
            
            

            if ($order_id) {
                $success = "Order #$order_id created successfully. Total : RM $total_amount";
                // Clear fields after success
                $customer_name = $customer_email = $customer_phone = $shipping_address = $payment_method = $payment_status = '';
                $order_items = [];
                $product_ids = [];
                $quantities = [];
            } else {
                $errors[] = "Failed to create order.";
            }
        }
    }
} catch (Exception $e) {
    $errors[] = "An unexpected error occurred: " . $e->getMessage();
}

// Include header
require_once __DIR__ . '/../../templates/header.php';
?>

<style>
    /* Reset some default */
    input, select, textarea, button {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 1rem;
    }

    .prod_container {
        max-width: 700px;
        margin: 40px auto;
        padding: 25px 30px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        border-radius: 8px;
    }

    h2 {
        color: #2c3e50;
        margin-bottom: 25px;
        font-weight: 700;
        text-align: center;
        letter-spacing: 1px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        color: #34495e;
        font-weight: 600;
    }

    input[type="text"],
    input[type="email"],
    textarea,
    select {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #ccc;
        border-radius: 5px;
        transition: border-color 0.3s ease;
        box-sizing: border-box;
        outline-offset: 2px;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    textarea:focus,
    select:focus {
        border-color: #3498db;
        box-shadow: 0 0 5px rgba(52, 152, 219, 0.4);
    }

    textarea {
        resize: vertical;
        min-height: 70px;
    }

    .error-box {
        background: #ffebee;
        color: #b71c1c;
        border: 1px solid #f44336;
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: 600;
        list-style-type: disc;
    }

    .success-box {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #4caf50;
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: 600;
        text-align: center;
    }

    form button[type="submit"] {
        background-color: #3498db;
        border: none;
        color: white;
        padding: 12px 22px;
        font-size: 1.1rem;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 15px;
        width: 100%;
    }

    form button[type="submit"]:hover {
        background-color: #2980b9;
    }

    #add-product-btn {
        background-color: #2ecc71;
        border: none;
        color: white;
        padding: 8px 16px;
        font-size: 1rem;
        border-radius: 5px;
        cursor: pointer;
        margin-bottom: 20px;
        transition: background-color 0.3s ease;
        display: inline-block;
    }

    #add-product-btn:hover {
        background-color: #27ae60;
    }

    .product-row {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        gap: 10px;
    }

    .product-row select {
        flex-grow: 1;
    }

    .product-row input[type="number"] {
        width: 80px;
        padding: 8px 10px;
        border-radius: 5px;
        border: 1.5px solid #ccc;
        text-align: center;
    }

    .product-row button {
        background-color: #e74c3c;
        border: none;
        color: white;
        padding: 8px 12px;
        font-size: 0.9rem;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .product-row button:hover {
        background-color: #c0392b;
    }

    @media (max-width: 600px) {
        .product-row {
            flex-direction: column;
            align-items: stretch;
        }
        .product-row input[type="number"] {
            width: 100%;
        }
        .product-row button {
            width: 100%;
            margin-top: 6px;
        }
    }
</style>

<div class="prod_container">
    <h2>Create New Order</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
        </div>
    <?php elseif ($success): ?>
        <div class="success-box"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <label for="customer_name">Customer Name:</label>
        <input type="text" id="customer_name" name="customer_name" required value="<?= htmlspecialchars($customer_name ?? '') ?>">

        <label for="customer_email">Customer Email:</label>
        <input type="email" id="customer_email" name="customer_email" required value="<?= htmlspecialchars($customer_email ?? '') ?>">

        <label for="customer_phone">Customer Phone:</label>
        <input type="text" id="customer_phone" name="customer_phone" required value="<?= htmlspecialchars($customer_phone ?? '') ?>">

        <label for="shipping_address">Shipping Address:</label>
        <textarea id="shipping_address" name="shipping_address" rows="3" required><?= htmlspecialchars($shipping_address ?? '') ?></textarea>

        <label for="payment_method">Payment Method:</label>
        <select id="payment_method" name="payment_method" required>
            <option value="" disabled <?= empty($payment_method) ? 'selected' : '' ?>>Select payment method</option>
            <option value="Credit Card" <?= ($payment_method ?? '') === 'Credit Card' ? 'selected' : '' ?>>Credit Card</option>
            <option value="Cash On Delivery" <?= ($payment_method ?? '') === 'Cash On Delivery' ? 'selected' : '' ?>>Cash On Delivery</option>
            <option value="Paypal" <?= ($payment_method ?? '') === 'Paypal' ? 'selected' : '' ?>>Paypal</option>
        </select>

        <!-- <label for="payment_status">Payment Status:</label>
        <select id="payment_status" name="payment_status" required>
            <option value="" disabled <?= empty($payment_status) ? 'selected' : '' ?>>Select payment status</option>
            <option value="Paid" <?= ($payment_status ?? '') === 'Paid' ? 'selected' : '' ?>>Paid</option>
            <option value="Pending" <?= ($payment_status ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Failed" <?= ($payment_status ?? '') === 'Failed' ? 'selected' : '' ?>>Failed</option>
        </select> -->

        <h3 style="margin-top: 30px;">Order Items</h3>
        <div id="product-list">
            <?php if (!empty($product_ids ?? [])): ?>
                <?php foreach ($product_ids as $i => $pid): 
                    $qty = $quantities[$i] ?? 1;
                ?>
                <div class="product-row">
                    <select name="product_id[]" required>
                        <option value="" disabled>Select product</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>" <?= $pid == $product['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($product['name']) ?> (Stock: <?= $product['stock_quantity'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="quantity[]" min="1" value="<?= intval($qty) ?>" required>
                    <button type="button" onclick="removeProductRow(this)">Remove</button>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="product-row">
                    <select name="product_id[]" required>
                        <option value="" disabled selected>Select product</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>">
                                <?= htmlspecialchars($product['name']) ?> (Stock: <?= $product['stock_quantity'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="quantity[]" min="1" value="1" required>
                    <button type="button" onclick="removeProductRow(this)">Remove</button>
                </div>
            <?php endif; ?>
        </div>
        <button type="button" id="add-product-btn">Add Product</button>

        <button type="submit">Create Order</button>
    </form>
</div>

<script>
function addProductRow() {
    const row = document.querySelector('.product-row');
    if (!row) return;
    const clone = row.cloneNode(true);
    clone.querySelector('select').value = "";
    clone.querySelector('input[type=number]').value = 1;
    document.getElementById('product-list').appendChild(clone);
}

function removeProductRow(btn) {
    const rows = document.querySelectorAll('.product-row');
    if (rows.length > 1) {
        btn.parentElement.remove();
    } else {
        alert("At least one product item is required.");
    }
}

document.getElementById('add-product-btn').addEventListener('click', addProductRow);
</script>

<?php
    // Include footer
    require_once __DIR__ . '/../../templates/footer.php';
}
?>
