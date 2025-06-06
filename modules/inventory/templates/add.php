<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../functions.php';

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
                                required>
                            <option value="">-- Choose a product --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['name']) ?> 
                                    (Current: <?= $p['stock_quantity'] ?> units)
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                               required>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            This will be added to the current stock
                        </p>
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
                                class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-50 transition-colors duration-200">
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


