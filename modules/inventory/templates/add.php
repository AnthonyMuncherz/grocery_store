<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../functions.php';

$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if ($product_id && $quantity !== '') {
        $success = addStockToProduct($product_id, $quantity, $notes);
        if (!$success) {
            $error = "Failed to add stock.";
        }
    } else {
        $error = "Please fill all fields.";
    }
}

$products = getProductsWithStock();
?>

<div class="max-w-2xl mx-auto mt-8 p-6 bg-white shadow rounded-lg">
    <h1 class="text-2xl font-bold mb-4 text-red-600">Add Stock</h1>

    <?php if ($success): ?>
        <div class="p-4 mb-4 bg-green-100 text-green-800 rounded">Stock added successfully!</div>
    <?php elseif ($error): ?>
        <div class="p-4 mb-4 bg-red-100 text-red-800 rounded"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block font-semibold">Product</label>
            <select name="product_id" class="w-full border px-3 py-2 rounded" required>
                <option value="">-- Select Product --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= $p['stock_quantity'] ?> in stock)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block font-semibold">Quantity to Add</label>
            <input type="number" name="quantity" class="w-full border px-3 py-2 rounded" required>
        </div>
        <div>
            <label class="block font-semibold">Notes (optional)</label>
            <textarea name="notes" class="w-full border px-3 py-2 rounded"></textarea>
        </div>

        <div class="pt-4">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add Stock</button>
            <a href="/grocery_store/index.php?module=inventory" class="ml-2 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Cancel</a>
        </div>
    </form>
</div>
