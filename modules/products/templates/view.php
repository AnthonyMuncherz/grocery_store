<?php
/**
 * Product View Template - Individual Product Details
 */
?>
<div class="product-view-container py-6">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="index.php" class="hover:text-theme-red">Home</a></li>
                <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
                <li><a href="index.php?module=products" class="hover:text-theme-red">Products</a></li>
                <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
                <li class="text-gray-800 font-medium"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>

        <!-- Product Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Image -->
            <div class="product-image">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <img src="assets/images/products/<?= htmlspecialchars($product['image_url'] ?: 'default.jpg') ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         class="w-full h-96 object-cover">
                </div>
                
                <!-- Product Meta Info -->
                <div class="mt-4 text-sm text-gray-600 space-y-1">
                    <div><strong>Product ID:</strong> #<?= $product['id'] ?></div>
                    <div><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></div>
                    <div><strong>Date Added:</strong> <?= date('d M Y', strtotime($product['created_at'])) ?></div>
                </div>
            </div>

            <!-- Product Information -->
            <div class="product-info">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <!-- Product Name -->
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">
                        <?= htmlspecialchars($product['name']) ?>
                    </h1>
                    
                    <!-- Product Category -->
                    <div class="text-sm text-gray-600 mb-4">
                        <span class="bg-gray-100 px-2 py-1 rounded">
                            <?= htmlspecialchars($product['category_name']) ?>
                        </span>
                    </div>

                    <!-- Product Price -->
                    <div class="mb-6">
                        <div class="text-3xl font-bold text-theme-red mb-2">
                            <?= formatProductPrice($product['price']) ?>
                        </div>
                        <div class="text-sm text-gray-600">
                            Price includes GST
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-6">
                        <?php
                        $stock_qty = $product['stock_quantity'];
                        if ($stock_qty <= 0) {
                            $stock_class = 'bg-red-100 text-red-700';
                            $stock_text = 'Out of Stock';
                            $stock_icon = 'fas fa-times-circle';
                        } elseif ($stock_qty <= 20) {
                            $stock_class = 'bg-yellow-100 text-yellow-700';
                            $stock_text = "Low Stock ($stock_qty remaining)";
                            $stock_icon = 'fas fa-exclamation-triangle';
                        } else {
                            $stock_class = 'bg-green-100 text-green-700';
                            $stock_text = "In Stock ($stock_qty available)";
                            $stock_icon = 'fas fa-check-circle';
                        }
                        ?>
                        <div class="<?= $stock_class ?> px-3 py-2 rounded-lg inline-flex items-center">
                            <i class="<?= $stock_icon ?> mr-2"></i>
                            <span class="font-medium"><?= $stock_text ?></span>
                        </div>
                    </div>

                    <!-- Product Description -->
                    <?php if (!empty($product['description'])): ?>
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                            <div class="text-gray-700 leading-relaxed">
                                <?= nl2br(htmlspecialchars($product['description'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Add to Cart Section -->
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <div class="add-to-cart-section border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Add to Cart</h3>
                            
                            <div class="flex items-center space-x-4 mb-4">
                                <label for="product-quantity" class="text-sm font-medium text-gray-700">
                                    Quantity:
                                </label>
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="decreaseProductQuantity()" 
                                            class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center">
                                        <i class="fas fa-minus text-sm"></i>
                                    </button>
                                    <input type="number" id="product-quantity" 
                                           value="1" min="1" max="<?= $product['stock_quantity'] ?>"
                                           class="w-20 text-center border border-gray-300 rounded px-3 py-2">
                                    <button type="button" onclick="increaseProductQuantity()" 
                                            class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                </div>
                                <div class="text-sm text-gray-600">
                                    Max: <?= $product['stock_quantity'] ?>
                                </div>
                            </div>

                            <button onclick="addToCartWithQuantity(<?= $product['id'] ?>)" 
                                    class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                                <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="out-of-stock-section border-t pt-6">
                            <button disabled 
                                    class="w-full bg-gray-300 text-gray-500 font-semibold py-3 px-6 rounded-lg cursor-not-allowed">
                                <i class="fas fa-times-circle mr-2"></i>Out of Stock
                            </button>
                            <p class="text-sm text-gray-600 mt-2 text-center">
                                This product is currently out of stock. Please check back later.
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Additional Information -->
                    <div class="additional-info mt-6 text-sm text-gray-600 space-y-2">
                        <div class="flex items-center">
                            <i class="fas fa-truck mr-2 text-green-600"></i>
                            <span>FREE delivery within Klang Valley</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt mr-2 text-blue-600"></i>
                            <span>100% Authentic products</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone mr-2 text-red-600"></i>
                            <span>Customer support: +60 3-1234 5678</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
            <div class="related-products mt-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Related Products</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php foreach ($related_products as $related): ?>
                        <div class="product-card bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                            <div class="product-image relative">
                                <a href="index.php?module=products&action=view&id=<?= $related['id'] ?>">
                                    <img src="assets/images/products/<?= htmlspecialchars($related['image_url'] ?: 'default.jpg') ?>" 
                                         alt="<?= htmlspecialchars($related['name']) ?>"
                                         class="w-full h-32 object-cover">
                                </a>
                            </div>
                            <div class="p-3">
                                <h3 class="font-medium text-gray-800 text-sm mb-1 truncate">
                                    <a href="index.php?module=products&action=view&id=<?= $related['id'] ?>" 
                                       class="hover:text-theme-red">
                                        <?= htmlspecialchars($related['name']) ?>
                                    </a>
                                </h3>
                                <p class="text-theme-red font-bold text-sm mb-2">
                                    <?= formatProductPrice($related['price']) ?>
                                </p>
                                <?php if ($related['stock_quantity'] > 0): ?>
                                    <button onclick="addToCart(<?= $related['id'] ?>)" 
                                            class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-1 px-2 rounded text-xs">
                                        <i class="fas fa-cart-plus mr-1"></i>Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button disabled 
                                            class="w-full bg-gray-300 text-gray-500 font-medium py-1 px-2 rounded text-xs cursor-not-allowed">
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div> 