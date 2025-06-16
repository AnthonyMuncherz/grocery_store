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
        <form method="POST" action="index.php?module=inventory&action=add_product" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

            <!-- Product Image -->
            <div class="md:col-span-2">
                <label for="product_image" class="block font-medium mb-1">Product Image</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="product_image" class="relative cursor-pointer bg-white rounded-md font-medium text-theme-red hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-theme-red">
                                <span>Upload a file</span>
                                <input id="product_image" name="product_image" type="file" accept="image/*" class="sr-only" onchange="previewImage(event)">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF, WebP up to 5MB</p>
                    </div>
                </div>
                <!-- Image Preview -->
                <div id="imagePreview" class="mt-2 hidden">
                    <img id="previewImg" src="" alt="Preview" class="max-w-xs max-h-32 rounded-lg shadow-sm">
                </div>
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
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Image</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Product</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($inventory_items)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-6">No inventory items found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inventory_items as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <?php if (!empty($item['image_url']) && file_exists($item['image_url'])): ?>
                                    <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>" 
                                         class="w-12 h-12 object-cover rounded-lg shadow-sm">
                                <?php else: ?>
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
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

function previewImage(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (file) {
        // Check file size (5MB = 5 * 1024 * 1024 bytes)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            event.target.value = '';
            previewContainer.classList.add('hidden');
            return;
        }
        
        // Check file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Only JPEG, PNG, GIF, and WebP images are allowed');
            event.target.value = '';
            previewContainer.classList.add('hidden');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.classList.add('hidden');
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