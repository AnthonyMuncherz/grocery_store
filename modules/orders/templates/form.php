<style>
.container {
    max-width: 600px;
    margin: 30px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-family: Arial, sans-serif;
    background: #f9f9f9;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

label {
    font-weight: bold;
    display: block;
    margin-top: 15px;
    margin-bottom: 6px;
    color: #555;
}

input[type="text"],
select,
input[type="number"] {
    width: calc(100% - 12px);
    padding: 6px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

.product-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.product-row select {
    flex: 1;
}

.product-row input[type="number"] {
    width: 70px;
}

.product-row button {
    background-color: #ff4c4c;
    border: none;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.product-row button:hover {
    background-color: #e04343;
}

button[type="button"]#add-product-btn {
    background-color: #4caf50;
    border: none;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 15px;
    margin-top: 10px;
}

button[type="button"]#add-product-btn:hover {
    background-color: #449d44;
}

button[type="submit"] {
    margin-top: 25px;
    width: 100%;
    padding: 10px;
    font-size: 16px;
    background-color: #007bff;
    border: none;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}

button[type="submit"]:hover {
    background-color: #0069d9;
}

.error-box, .success-box {
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.error-box {
    background-color: #ffe6e6;
    border: 1px solid #ff4c4c;
    color: #a94442;
}

.success-box {
    background-color: #e6ffea;
    border: 1px solid #4caf50;
    color: #2e7d32;
}
</style>

<div class="container">
    <h2>Create New Order</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
        </div>
    <?php elseif ($success): ?>
        <div class="success-box"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="customer_name">Customer Name:</label>
        <input type="text" id="customer_name" name="customer_name" required>

        <h3 style="margin-top:30px;">Order Items</h3>
        <div id="product-list">
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
        </div>
        <button type="button" id="add-product-btn" onclick="addProductRow()">+ Add Product</button>

        <button type="submit">Create Order</button>
    </form>
</div>

<script>
function addProductRow() {
    const row = document.querySelector('.product-row');
    const clone = row.cloneNode(true);
    clone.querySelector('select').value = ""; // reset select
    clone.querySelector('input[type=number]').value = 1; // reset quantity
    document.getElementById('product-list').appendChild(clone);
}

function removeProductRow(btn) {
    const rows = document.querySelectorAll('.product-row');
    if (rows.length > 1) {
        btn.parentElement.remove();
    }
}
</script>
