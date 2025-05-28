<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../functions.php';

$product = null;
if (isset($_GET['id'])) {
    $product = getProductById($_GET['id']);
}

if (!$product) {
    echo "<div class='p-6 text-red-600'>Product not found.</div>";
    return;
}
?>

<div class="max-w-4xl mx-auto mt-8 p-6 bg-white shadow rounded-lg">
    <h1 class="text-2xl font-bold mb-4 text-red-600">Product Details</h1>

    <table class="table-auto w-full text-left border">
        <tr><th class="px-4 py-2 border">Name</th><td class="px-4 py-2 border"><?= htmlspecialchars($product['name']) ?></td></tr>
        <tr><th class="px-4 py-2 border">Category</th><td class="px-4 py-2 border"><?= htmlspecialchars($product['category_id']) ?></td></tr>
        <tr><th class="px-4 py-2 border">Price</th><td class="px-4 py-2 border">RM <?= number_format($product['price'], 2) ?></td></tr>
        <tr><th class="px-4 py-2 border">Stock</th><td class="px-4 py-2 border"><?= htmlspecialchars($product['stock_quantity']) ?></td></tr>
    </table>

    <div class="mt-6">
    <a href="/grocery_store/index.php?module=inventory" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 inline-block">
    ← Back
    </a>
</div>
</div>
