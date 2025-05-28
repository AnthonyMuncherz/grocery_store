<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../functions.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo '<div class="max-w-4xl mx-auto mt-6 bg-white p-6 rounded shadow">';
    echo '<div class="text-center py-12">';
    echo '<i class="fas fa-exclamation-triangle text-6xl text-gray-400 mb-4"></i>';
    echo '<h2 class="text-2xl font-bold text-gray-700 mb-2">Invalid Product ID</h2>';
    echo '<p class="text-gray-600 mb-6">The product ID provided is not valid.</p>';
    echo '<a href="index.php?module=inventory" class="bg-theme-red hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-md">';
    echo '<i class="fas fa-arrow-left mr-2"></i>Back to Inventory';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    return;
}

$product = getProductById($id);

if (!$product) {
    echo '<div class="max-w-4xl mx-auto mt-6 bg-white p-6 rounded shadow">';
    echo '<div class="text-center py-12">';
    echo '<i class="fas fa-exclamation-triangle text-6xl text-gray-400 mb-4"></i>';
    echo '<h2 class="text-2xl font-bold text-gray-700 mb-2">Product Not Found</h2>';
    echo '<p class="text-gray-600 mb-6">The product you\'re looking for doesn\'t exist or has been deleted.</p>';
    echo '<a href="index.php?module=inventory" class="bg-theme-red hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-md">';
    echo '<i class="fas fa-arrow-left mr-2"></i>Back to Inventory';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    return;
}
?>

<div class="max-w-6xl mx-auto mt-6 bg-white p-6 rounded-lg shadow-lg">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Information Panel -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-xl border">
            <div class="flex items-center mb-6">
                <div class="bg-theme-red text-white p-3 rounded-lg mr-4">
                    <i class="fas fa-box text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="text-gray-600">Product Details</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-tag mr-2 text-gray-500"></i>Product ID:
                    </span>
                    <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm font-mono">
                        <?= htmlspecialchars($product['id']) ?>
                    </span>
                </div>
                
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-list mr-2 text-gray-500"></i>Category ID:
                    </span>
                    <span class="text-gray-900 font-medium"><?= htmlspecialchars($product['category_id']) ?></span>
                </div>
                
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-money-bill-wave mr-2 text-gray-500"></i>Price:
                    </span>
                    <span class="text-2xl font-bold text-green-600">RM <?= number_format($product['price'], 2) ?></span>
                </div>
                
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-warehouse mr-2 text-gray-500"></i>Current Stock:
                    </span>
                    <div class="text-right">
                        <span class="text-3xl font-bold <?= $product['stock_quantity'] > 10 ? 'text-green-600' : ($product['stock_quantity'] > 0 ? 'text-yellow-600' : 'text-red-600') ?>">
                            <?= $product['stock_quantity'] ?>
                        </span>
                        <span class="text-gray-600 ml-1">units</span>
                        <?php if ($product['stock_quantity'] <= 10): ?>
                            <div class="mt-1">
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Low Stock
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($product['description']): ?>
                <div class="py-3">
                    <span class="font-semibold text-gray-700 flex items-center mb-2">
                        <i class="fas fa-info-circle mr-2 text-gray-500"></i>Description:
                    </span>
                    <p class="text-gray-900 bg-white p-3 rounded-lg border italic">
                        "<?= htmlspecialchars($product['description']) ?>"
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stock Adjustment Panel -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border">
            <div class="flex items-center mb-6">
                <div class="bg-blue-600 text-white p-3 rounded-lg mr-4">
                    <i class="fas fa-plus-minus text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-blue-800">Stock Management</h3>
                    <p class="text-blue-600">Adjust inventory levels</p>
                </div>
            </div>
            
            <form method="POST" action="index.php?module=inventory&action=update_stock" class="space-y-6">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                
                <div class="bg-white p-4 rounded-lg border">
                    <label class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fas fa-calculator mr-2"></i>Stock Adjustment:
                    </label>
                    <div class="relative">
                        <input type="number" 
                               name="adjustment" 
                               id="adjustment_input"
                               class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-lg font-semibold text-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
                               placeholder="Enter quantity change"
                               required
                               min="-<?= $product['stock_quantity'] ?>"
                               onkeyup="updatePreview()">
                        <div class="absolute right-3 top-3 text-gray-400">
                            <i class="fas fa-hashtag"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-600 bg-gray-50 p-2 rounded">
                        <i class="fas fa-lightbulb mr-1 text-yellow-500"></i>
                        <strong>Tip:</strong> Use positive numbers to add stock, negative to remove stock.
                        <br>Current stock: <?= $product['stock_quantity'] ?> units. Cannot go below 0.
                    </div>
                    
                    <!-- Live Preview -->
                    <div id="stock_preview" class="mt-3 p-3 bg-gray-100 rounded-lg border hidden">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700">New Stock Level:</span>
                            <span id="preview_amount" class="text-lg font-bold text-blue-600"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border">
                    <label class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fas fa-sticky-note mr-2"></i>Note (Optional):
                    </label>
                    <textarea name="notes" 
                              class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
                              rows="4"
                              placeholder="Add a note about this stock adjustment (e.g., 'Received new shipment', 'Damaged goods removed', etc.)"></textarea>
                </div>

                <button type="submit" 
                        name="update_stock"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-save mr-2"></i>Update Stock Level
                </button>
            </form>
        </div>
    </div>

    <!-- Action Buttons Section -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="index.php?module=inventory"
               class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
            </a>
            
            <a href="index.php?module=inventory&action=view&id=<?= $product['id'] ?>"
               class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md flex items-center">
                <i class="fas fa-eye mr-2"></i>View Details
            </a>
            
            <button onclick="toggleDangerActions()" 
                    class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md flex items-center">
                <i class="fas fa-cog mr-2"></i>Product Actions
            </button>
        </div>
        
        <!-- Hidden Danger Actions -->
        <div id="danger_actions" class="hidden mt-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg">
            <h4 class="text-lg font-bold text-red-700 mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone
            </h4>
            <div class="flex flex-wrap gap-3 justify-center">
                <form method="POST" action="index.php?module=inventory&action=soft_delete" 
                      onsubmit="return confirm('Are you sure you want to hide this product? It will be hidden but not permanently removed.');" 
                      class="inline">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" 
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg transition-all duration-200 flex items-center">
                        <i class="fas fa-eye-slash mr-2"></i>Hide Product
                    </button>
                </form>

                <form method="POST" action="index.php?module=inventory&action=hard_delete" 
                      onsubmit="return confirm('⚠️ This will permanently delete the product and cannot be undone. Are you sure you want to proceed?');" 
                      class="inline">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition-all duration-200 flex items-center">
                        <i class="fas fa-trash mr-2"></i>Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updatePreview() {
    const input = document.getElementById('adjustment_input');
    const preview = document.getElementById('stock_preview');
    const previewAmount = document.getElementById('preview_amount');
    const currentStock = <?= $product['stock_quantity'] ?>;
    
    if (input.value !== '') {
        const adjustment = parseInt(input.value) || 0;
        const newStock = currentStock + adjustment;
        
        preview.classList.remove('hidden');
        previewAmount.textContent = newStock + ' units';
        
        // Color coding
        if (newStock < 0) {
            previewAmount.className = 'text-lg font-bold text-red-600';
        } else if (newStock <= 10) {
            previewAmount.className = 'text-lg font-bold text-yellow-600';
        } else {
            previewAmount.className = 'text-lg font-bold text-green-600';
        }
    } else {
        preview.classList.add('hidden');
    }
}

function toggleDangerActions() {
    const dangerActions = document.getElementById('danger_actions');
    dangerActions.classList.toggle('hidden');
}
</script>
