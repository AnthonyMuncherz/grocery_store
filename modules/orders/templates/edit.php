<?php
/**
 * Order Edit Template
 */

// Get any edit errors from session
$edit_errors = $_SESSION['edit_errors'] ?? [];
unset($_SESSION['edit_errors']);
?>

<style>
    /* Reset some default */
    input, select, textarea, button {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 1rem;
    }

    .edit_container {
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

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e1e8ed;
    }

    .back-btn {
        background: #6c757d;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .back-btn:hover {
        background: #5a6268;
        text-decoration: none;
        color: white;
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
        margin-bottom: 15px;
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

    .info-box {
        background: #e3f2fd;
        color: #1565c0;
        border: 1px solid #2196f3;
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: 600;
    }

    form button[type="submit"] {
        background-color: #f39c12;
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
        background-color: #d68910;
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
        margin-bottom: 0;
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

    .status-select {
        background-color: #f8f9fa;
        border: 2px solid #dee2e6;
        color: #495057;
        font-weight: 600;
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
        .order-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
    }
</style>

<div class="edit_container">
    <div class="order-header">
        <h2>✏️ Edit Order #<?= htmlspecialchars($order['id']) ?></h2>
        <a href="index.php?module=orders&action=view&id=<?= $order['id'] ?>" class="back-btn">← Back to Order</a>
    </div>

    <div class="info-box">
        <strong>📝 Editing Mode:</strong> You can modify customer details, order items, and order status. Changes will be saved when you submit the form.
    </div>

    <?php if (!empty($edit_errors)): ?>
        <div class="error-box">
            <ul><?php foreach ($edit_errors as $error) echo "<li>" . htmlspecialchars($error) . "</li>"; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php?module=orders&action=update" novalidate>
        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
        
        <label for="customer_name">Customer Name:</label>
        <input type="text" id="customer_name" name="customer_name" required 
               value="<?= htmlspecialchars($order['customer_name']) ?>">

        <label for="customer_email">Customer Email:</label>
        <input type="email" id="customer_email" name="customer_email" required 
               value="<?= htmlspecialchars($order['customer_email']) ?>">

        <label for="customer_phone">Customer Phone:</label>
        <input type="text" id="customer_phone" name="customer_phone" required 
               value="<?= htmlspecialchars($order['customer_phone']) ?>">

        <label for="shipping_address">Shipping Address:</label>
        <textarea id="shipping_address" name="shipping_address" rows="3" required><?= htmlspecialchars($order['shipping_address']) ?></textarea>

        <label for="payment_method">Payment Method:</label>
        <select id="payment_method" name="payment_method" required>
            <option value="" disabled>Select payment method</option>
            <option value="Credit Card" <?= $order['payment_method'] === 'Credit Card' ? 'selected' : '' ?>>Credit Card</option>
            <option value="Cash On Delivery" <?= $order['payment_method'] === 'Cash On Delivery' ? 'selected' : '' ?>>Cash On Delivery</option>
            <option value="FPX" <?= $order['payment_method'] === 'FPX' ? 'selected' : '' ?>>FPX</option>
            <option value="Touch n Go" <?= $order['payment_method'] === 'Touch n Go' ? 'selected' : '' ?>>Touch n Go</option>
            <option value="Boost" <?= $order['payment_method'] === 'Boost' ? 'selected' : '' ?>>Boost</option>
            <option value="GrabPay" <?= $order['payment_method'] === 'GrabPay' ? 'selected' : '' ?>>GrabPay</option>
        </select>

        <label for="status">Order Status:</label>
        <select id="status" name="status" required class="status-select">
            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>

        <h3 style="margin-top: 30px;">Order Items</h3>
        <div id="product-list">
            <?php if (!empty($order['items'])): ?>
                <?php foreach ($order['items'] as $item): ?>
                <div class="product-row">
                    <select name="product_id[]" required>
                        <option value="" disabled>Select product</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>" <?= $item['product_id'] == $product['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($product['name']) ?> (Stock: <?= $product['stock_quantity'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="quantity[]" min="1" value="<?= htmlspecialchars($item['quantity']) ?>" required>
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

        <button type="submit">💾 Update Order</button>
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

// Add confirmation for significant status changes
document.getElementById('status').addEventListener('change', function() {
    const newStatus = this.value;
    const currentStatus = '<?= $order['status'] ?>';
    
    if (currentStatus !== newStatus && (newStatus === 'cancelled' || newStatus === 'delivered')) {
        if (!confirm(`Are you sure you want to change the order status to "${newStatus}"? This action may have significant implications.`)) {
            this.value = currentStatus;
        }
    }
});
</script> 