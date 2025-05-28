<?php
// Inventory listing page
$category_id = $_GET['category'] ?? null;
$search = $_GET['search'] ?? null;

$categories = getCategories();
$inventory_items = getInventoryItems($category_id, $search);
?>

<div class="inventory-container py-6">
    <!-- System Messages -->
    <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-200 text-red-800 rounded">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
        <!-- Search/Filters -->
        <div class="flex-grow p-4 bg-white rounded-lg shadow">
            <form method="GET" action="index.php" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="module" value="inventory">
                
                <!-- Search Input -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search Product</label>
                    <input type="text" name="search" id="search" 
                           value="<?= htmlspecialchars($search ?? '') ?>"
                           placeholder="e.g., Beras, Gula"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-theme-red focus:border-theme-red sm:text-sm">
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" id="category"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-theme-red focus:border-theme-red sm:text-sm">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($category_id == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 md:col-span-2">
                    <button type="submit"
                            class="w-full bg-theme-red hover:bg-theme-red-dark text-white font-semibold py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-theme-red">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>

                    <?php if (!empty($search) || !empty($category_id)): ?>
                        <a href="index.php?module=inventory"
                           class="w-full text-center bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                            Clear Filters
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>


                <!-- Action Buttons Container -->
                <div class="flex flex-col md:flex-row gap-3 h-fit self-end">
                <!-- Add New Product -->
                    <button onclick="toggleProductForm()" 
                        class="bg-theme-red hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg shadow transition-all inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>Add New Product
                     </button>

                <!-- Add Stock -->
                <a href="index.php?module=inventory&action=add" 
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg shadow transition-all inline-flex items-center">
                        <i class="fas fa-cubes mr-2"></i>Add Stock
                </a>
                </div>


    </div>

    <!-- Collapsible Add Product Form -->
    <div id="addProductForm" class="hidden add-product-form mb-6 p-4 bg-white rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4 border-b pb-2">New Product Details</h2>
        <form method="POST" action="index.php?module=inventory&action=add_product" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Product ID -->
            <div>
                <label for="product_id" class="block font-medium mb-1">Product ID (optional)</label>
                <input type="text" name="product_id" id="product_id" 
                       placeholder="Auto-generated if empty"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-theme-red focus:border-theme-red">
            </div>

            <!-- Product Name -->
            <div>
                <label for="name" class="block font-medium mb-1">Product Name *</label>
                <input type="text" name="name" id="name" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-theme-red focus:border-theme-red">
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block font-medium mb-1">Description</label>
                <textarea name="description" id="description" rows="2"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-theme-red focus:border-theme-red"></textarea>
            </div>

            <!-- Price -->
            <div>
                <label for="price" class="block font-medium mb-1">Price (MYR) *</label>
                <input type="number" step="0.01" min="0" name="price" id="price" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-theme-red focus:border-theme-red">
            </div>

            <!-- Category -->
            <div>
                <label for="category_id" class="block font-medium mb-1">Category *</label>
                <select name="category_id" id="category_id" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-theme-red focus:border-theme-red">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['id']) ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Stock Quantity -->
            <div>
                <label for="stock_quantity" class="block font-medium mb-1">Initial Stock *</label>
                <input type="number" min="0" name="stock_quantity" id="stock_quantity" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-theme-red focus:border-theme-red">
            </div>

            <!-- Submit Button -->
            <div class="md:col-span-2 pt-4">
                <button type="submit"
                        class="w-full bg-theme-red hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg shadow">
                    Add Product
                </button>
            </div>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-theme-red text-white">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Product</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($inventory_items)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-gray-500 py-6">No inventory items found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inventory_items as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800 font-medium"><?= htmlspecialchars($item['name']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($item['category_name']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <span class="px-2 py-1 rounded-full <?= $item['stock_quantity'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $item['stock_quantity'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex gap-3">
                                    <a href="index.php?module=inventory&action=view&id=<?= $item['id'] ?>"
                                       class="text-theme-red hover:text-red-700">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="index.php?module=inventory&action=edit&id=<?= $item['id'] ?>"
                                       class="text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleProductForm() {
    const form = document.getElementById('addProductForm');
    form.classList.toggle('hidden');
    
    // Smooth scroll to form
    if (!form.classList.contains('hidden')) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>

<style>
.add-product-form {
    transition: all 0.3s ease;
    max-height: 0;
    overflow: hidden;
}

.add-product-form.hidden {
    max-height: 0 !important;
    opacity: 0;
}

.add-product-form:not(.hidden) {
    max-height: 1000px;
    opacity: 1;
}

/* Mobile Floating Button */
@media (max-width: 768px) {
    .floating-add-btn {
        position: fixed;
        bottom: 2rem;
        right: 1rem;
        z-index: 50;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
}
</style>

<!-- Mobile Floating Action Button -->
<div class="md:hidden floating-add-btn">
    <button onclick="toggleProductForm()" 
            class="p-4 bg-theme-red text-white rounded-full shadow-lg hover:bg-red-700 transition">
        <i class="fas fa-plus text-xl"></i>
    </button>
</div>