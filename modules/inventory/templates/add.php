<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../functions.php';

$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');

    if ($product_id && $quantity > 0) {
        $success = addStockToProduct($product_id, $quantity, $notes);
        if ($success) {
            $_SESSION['success_message'] = "Stock added successfully! Added $quantity units to the selected product.";
            header("Location: index.php?module=inventory&action=list");
            exit;
        } else {
            $error = "Failed to add stock. Please try again.";
        }
    } else {
        if (!$product_id) {
            $error = "Please select a product.";
        } elseif ($quantity <= 0) {
            $error = "Quantity must be greater than zero.";
        }
    }
}

$products = getProductsWithStock();
$lowStockProducts = getLowStockProducts(10);
?>

<div class="max-w-4xl mx-auto mt-8 space-y-6">
    
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-theme-red">
                    <i class="fas fa-cubes mr-3"></i>Add Stock to Products
                </h1>
                <p class="text-gray-600 mt-1">Increase inventory quantities for existing products</p>
            </div>
            <a href="index.php?module=inventory" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-4 py-2 rounded-md transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
            </a>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <?php if (!empty($lowStockProducts)): ?>
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
            <div>
                <h3 class="text-lg font-semibold text-yellow-800">Low Stock Alert</h3>
                <p class="text-yellow-700">
                    <?= count($lowStockProducts) ?> product(s) have low stock (≤10 units). Consider restocking these items.
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Error/Success Messages -->
    <?php if ($success): ?>
        <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i>Stock added successfully!
        </div>
    <?php elseif ($error): ?>
        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Add Stock Form -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">
                    <i class="fas fa-plus mr-2"></i>Add Stock Form
                </h2>
                
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Select Product <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" 
                                class="w-full border border-gray-300 rounded-md px-3 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                required
                                onchange="updateProductInfo(this)">
                            <option value="">-- Choose a product --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>" 
                                        data-current-stock="<?= $p['stock_quantity'] ?>"
                                        data-price="<?= $p['price'] ?>">
                                    <?= htmlspecialchars($p['name']) ?> 
                                    (Current: <?= $p['stock_quantity'] ?> units)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Product Info Display -->
                    <div id="productInfo" class="hidden bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-2">Product Information</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Current Stock:</span>
                                <span id="currentStock" class="font-semibold">-</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Price:</span>
                                <span id="productPrice" class="font-semibold">-</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Quantity to Add <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="quantity" 
                               min="1" 
                               class="w-full border border-gray-300 rounded-md px-3 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Enter quantity to add"
                               required
                               onchange="calculateNewStock()">
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            This will be added to the current stock
                        </p>
                    </div>

                    <!-- New Stock Preview -->
                    <div id="stockPreview" class="hidden bg-blue-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">New Total Stock:</span>
                            <span id="newStockTotal" class="text-xl font-bold text-blue-600">-</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Notes (optional)
                        </label>
                        <textarea name="notes" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  rows="3"
                                  placeholder="Add a note about this stock addition (e.g., supplier, reason, etc.)"></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-md transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Add Stock
                        </button>
                        <button type="reset" 
                                class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-50 transition-colors duration-200"
                                onclick="resetForm()">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Low Stock Products Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>Low Stock Items
                </h3>
                
                <?php if (empty($lowStockProducts)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                        <p class="text-gray-600">All products are well stocked!</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <?php foreach ($lowStockProducts as $product): ?>
                            <div class="flex justify-between items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="font-medium text-sm"><?= htmlspecialchars($product['name']) ?></p>
                                    <p class="text-xs text-gray-600"><?= htmlspecialchars($product['category_name']) ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        <?= $product['stock_quantity'] == 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= $product['stock_quantity'] ?> left
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-600 text-center">
                            <i class="fas fa-lightbulb mr-1"></i>
                            Tip: Select these products from the dropdown to restock quickly
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function updateProductInfo(select) {
    const productInfo = document.getElementById('productInfo');
    const currentStock = document.getElementById('currentStock');
    const productPrice = document.getElementById('productPrice');
    
    if (select.value) {
        const option = select.options[select.selectedIndex];
        const stock = option.getAttribute('data-current-stock');
        const price = option.getAttribute('data-price');
        
        currentStock.textContent = stock + ' units';
        productPrice.textContent = 'RM ' + parseFloat(price).toFixed(2);
        productInfo.classList.remove('hidden');
        
        calculateNewStock();
    } else {
        productInfo.classList.add('hidden');
        document.getElementById('stockPreview').classList.add('hidden');
    }
}

function calculateNewStock() {
    const select = document.querySelector('select[name="product_id"]');
    const quantityInput = document.querySelector('input[name="quantity"]');
    const stockPreview = document.getElementById('stockPreview');
    const newStockTotal = document.getElementById('newStockTotal');
    
    if (select.value && quantityInput.value) {
        const option = select.options[select.selectedIndex];
        const currentStock = parseInt(option.getAttribute('data-current-stock'));
        const addQuantity = parseInt(quantityInput.value);
        
        if (!isNaN(currentStock) && !isNaN(addQuantity) && addQuantity > 0) {
            const newTotal = currentStock + addQuantity;
            newStockTotal.textContent = newTotal + ' units';
            stockPreview.classList.remove('hidden');
        } else {
            stockPreview.classList.add('hidden');
        }
    } else {
        stockPreview.classList.add('hidden');
    }
}

function resetForm() {
    document.getElementById('productInfo').classList.add('hidden');
    document.getElementById('stockPreview').classList.add('hidden');
}
</script>
