<?php
ob_start(); // Must be at the very top

require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../functions.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("Invalid product ID.");
}

$product = getProductById($id);

if (!$product) {
    die("Product not found.");
}

$message = '';
$error = '';

// Handle Soft Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $stmt = getDbConnection()->prepare("UPDATE products SET deleted_at = datetime('now') WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    if ($stmt->execute()) {
        $message = "The product has been soft deleted (hidden).";
    } else {
        $error = "Failed to soft delete product.";
    }
}

// Handle Hard Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hard_delete_product'])) {
    $stmt = getDbConnection()->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    if ($stmt->execute()) {
        $message = "The product was permanently deleted.";
        $product = null; // Mark as deleted to avoid showing form again
    } else {
        $error = "Failed to permanently delete product.";
    }
}

// Handle Stock Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $adjust_qty = intval($_POST['adjust_quantity'] ?? 0);
    $note = trim($_POST['note'] ?? '');

    if ($adjust_qty === 0) {
        $error = "Quantity must not be zero.";
    } else {
        $db = getDbConnection();
        $db->exec('BEGIN');
        try {
            $stmt = $db->prepare("UPDATE products SET stock_quantity = stock_quantity + :qty WHERE id = :id");
            $stmt->bindValue(':qty', $adjust_qty, SQLITE3_INTEGER);
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            $stmt = $db->prepare("INSERT INTO inventory_logs (product_id, action, quantity, notes, created_at)
                VALUES (:id, :action, :qty, :note, datetime('now'))");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->bindValue(':qty', abs($adjust_qty), SQLITE3_INTEGER);
            $stmt->bindValue(':note', $note, SQLITE3_TEXT);
            $stmt->bindValue(':action', $adjust_qty > 0 ? 'add' : 'remove', SQLITE3_TEXT);
            $stmt->execute();

            $db->exec('COMMIT');
            $message = "Stock updated successfully.";
            $product = getProductById($id);
        } catch (Exception $e) {
            $db->exec('ROLLBACK');
            $error = "Failed to update stock.";
        }
    }
}
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<div class="max-w-2xl mx-auto mt-6 bg-white p-6 rounded shadow">
    <?php if ($message): ?>
        <p class="mb-4 text-green-600"><?= htmlspecialchars($message) ?></p>
    <?php elseif ($error): ?>
        <p class="mb-4 text-red-600"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($product): ?>
        <h2 class="text-xl font-bold mb-4 text-red-600">Product: <?= htmlspecialchars($product['name']) ?></h2>

        <div class="mb-4">
            <p><strong>Category:</strong> <?= htmlspecialchars($product['category_id']) ?></p>
            <p><strong>Price:</strong> RM <?= number_format($product['price'], 2) ?></p>
            <p><strong>Current Stock:</strong> <?= $product['stock_quantity'] ?></p>
        </div>

        <!-- Stock Adjustment Form -->
        <form method="POST" class="mb-4">
            <label class="block mb-2 font-semibold">Adjust Stock Quantity:</label>
            <input type="number" name="adjust_quantity" class="border p-2 w-full mb-2" required>
            <small class="text-gray-600">Use negative number to reduce stock.</small>

            <label class="block mt-4 mb-2 font-semibold">Note (optional):</label>
            <textarea name="note" class="border p-2 w-full mb-2" rows="2"></textarea>

    <!-- All buttons inline within a single flex container -->
        <div class="flex flex-wrap gap-2 mt-4">
        <!-- Update Stock Form -->
        <form method="POST" class="inline">
        <button type="submit" name="update_stock" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update Stock
        </button>
        </form>

    <!-- Soft Delete Form -->
    <form method="POST" onsubmit="return confirm('Are you sure you want to soft delete this product?');" class="inline">
        <input type="hidden" name="delete_product" value="1">
        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
            Soft Delete
        </button>
    </form>

    <!-- Permanently Delete Form -->
    <form method="POST" onsubmit="return confirm('⚠️ This will permanently delete the product. Proceed?');" class="inline">
        <input type="hidden" name="hard_delete_product" value="1">
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
            Permanently Delete
        </button>
    </form>

    <!-- Back Link -->
    <a href="/grocery_store/index.php?module=inventory"
       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 inline-block">
        ← Back
    </a>
</div>

    <?php else: ?>
        <p class="text-gray-600 mb-4">Product not found or already deleted.</p>
        <a href="/grocery_store/index.php?module=inventory"
           class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 inline-block">
            ← Back
        </a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>
<?php ob_end_flush(); ?>
