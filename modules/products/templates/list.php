<?php
/**
 * Product listing template - Tailwind CSS Version
 */
?>
<div class="products-container py-6">
    <!-- Search and Filter Section -->
    <div class="filter-section mb-6 p-4 bg-white rounded-lg shadow">
        <form method="GET" action="index.php" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <input type="hidden" name="module" value="products">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search Products</label>
                <input type="text" name="search" id="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    placeholder="e.g., Milo, Kangkung"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-malaysia-blue focus:border-malaysia-blue sm:text-sm">
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category" id="category"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-malaysia-blue focus:border-malaysia-blue sm:text-sm">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:col-span-1">
                <button type="submit"
                    class="w-full bg-malaysia-blue hover:bg-malaysia-blue-dark text-white font-semibold py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-malaysia-blue">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>
            <?php if (!empty($_GET['search']) || !empty($_GET['category'])): ?>
                <div class="md:col-span-1">
                    <a href="index.php?module=products"
                        class="w-full inline-block text-center bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                        Clear Filters
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Products Grid -->
    <?php if (empty($products)): ?>
        <div class="no-products text-center py-10">
            <i class="fas fa-box-open fa-3x text-gray-400 mb-4"></i>
            <p class="text-xl text-gray-600">No products found matching your criteria.</p>
            <p class="text-gray-500">Try adjusting your search or filters.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
                <?php
                $stock_qty = $product['stock_quantity'];
                $stock_status_class = 'bg-green-100 text-green-700'; // Default: stock-high
                $stock_text = "In Stock ($stock_qty)";
                if ($stock_qty <= 0) {
                    $stock_status_class = 'bg-red-100 text-red-700';
                    $stock_text = "Out of Stock";
                } elseif ($stock_qty <= 20) { // stock-low threshold
                    $stock_status_class = 'bg-yellow-100 text-yellow-700'; // Assuming 20 is medium-low threshold
                    $stock_text = "Low Stock ($stock_qty)";
                } elseif ($stock_qty <= 50) { // stock-medium threshold
                    $stock_status_class = 'bg-blue-100 text-blue-700'; // Example for medium
                    $stock_text = "In Stock ($stock_qty)";
                }
                ?>
                <div
                    class="product-card bg-white rounded-lg shadow-md overflow-hidden flex flex-col transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="product-image relative">
                        <a href="index.php?module=products&action=view&id=<?= $product['id'] ?>">
                            <img src="assets/images/products/<?= htmlspecialchars($product['image_url'] ?: 'default.jpg') ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy" class="w-full h-48 object-cover">
                        </a>
                        <span class="absolute top-2 right-2 <?= $stock_status_class ?> px-2 py-1 text-xs font-semibold rounded">
                            <?= $stock_text ?>
                        </span>
                    </div>
                    <div class="product-info p-4 flex flex-col flex-grow">
                        <h3 class="product-name text-lg font-semibold text-gray-800 mb-1 truncate"
                            title="<?= htmlspecialchars($product['name']) ?>">
                            <a href="index.php?module=products&action=view&id=<?= $product['id'] ?>"
                                class="hover:text-malaysia-blue">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h3>
                        <p class="product-category text-xs text-gray-500 mb-2">
                            <?= htmlspecialchars($product['category_name']) ?></p>
                        <p class="product-description text-sm text-gray-600 mb-3 h-10 overflow-hidden">
                            <?= nl2br(htmlspecialchars(substr($product['description'] ?? '', 0, 50))) . (strlen($product['description'] ?? '') > 50 ? '...' : '') ?>
                        </p>
                        <div class="mt-auto">
                            <p class="product-price text-xl font-bold text-malaysia-blue mb-3">
                                <?= formatProductPrice($product['price']) ?></p>
                            <div class="product-actions space-y-2">
                                <a href="index.php?module=products&action=view&id=<?= $product['id'] ?>"
                                    class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md text-sm">
                                    View Details
                                </a>
                                <?php if ($product['stock_quantity'] > 0): ?>
                                    <button onclick="addToCart(<?= $product['id'] ?>)"
                                        class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md text-sm add-to-cart-button">
                                        <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button disabled
                                        class="w-full bg-gray-300 text-gray-500 font-semibold py-2 px-4 rounded-md text-sm cursor-not-allowed">
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination mt-8 flex justify-center items-center space-x-1">
            <?php
            // Previous page
            if ($current_page > 1) {
                echo '<a href="?module=products&page=' . ($current_page - 1) . (isset($_GET['category']) ? '&category=' . htmlspecialchars($_GET['category']) : '') . (isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : '') . '" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Previous</a>';
            } else {
                echo '<span class="px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-md cursor-not-allowed">Previous</span>';
            }

            // Page numbers
            $num_links = 2; // Number of links to show around current page
            $start = max(1, $current_page - $num_links);
            $end = min($total_pages, $current_page + $num_links);

            if ($start > 1) {
                echo '<a href="?module=products&page=1' . (isset($_GET['category']) ? '&category=' . htmlspecialchars($_GET['category']) : '') . (isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : '') . '" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">1</a>';
                if ($start > 2) {
                    echo '<span class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md">...</span>';
                }
            }

            for ($i = $start; $i <= $end; $i++) {
                $active_class = $current_page == $i ? 'bg-malaysia-blue text-white border-malaysia-blue' : 'text-gray-700 bg-white hover:bg-gray-50';
                echo '<a href="?module=products&page=' . $i . (isset($_GET['category']) ? '&category=' . htmlspecialchars($_GET['category']) : '') . (isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : '') . '" 
                   class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-md ' . $active_class . '">' . $i . '</a>';
            }

            if ($end < $total_pages) {
                if ($end < $total_pages - 1) {
                    echo '<span class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md">...</span>';
                }
                echo '<a href="?module=products&page=' . $total_pages . (isset($_GET['category']) ? '&category=' . htmlspecialchars($_GET['category']) : '') . (isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : '') . '" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">' . $total_pages . '</a>';
            }

            // Next page
            if ($current_page < $total_pages) {
                echo '<a href="?module=products&page=' . ($current_page + 1) . (isset($_GET['category']) ? '&category=' . htmlspecialchars($_GET['category']) : '') . (isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : '') . '" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Next</a>';
            } else {
                echo '<span class="px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-md cursor-not-allowed">Next</span>';
            }
            ?>
        </div>
    <?php endif; ?>
</div>