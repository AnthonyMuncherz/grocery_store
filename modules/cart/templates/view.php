<?php
/**
 * Cart View Template
 */
?>
<div class="cart-container py-6">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Cart Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-shopping-cart mr-2 text-theme-red"></i>Shopping Cart
            </h1>
            <div class="text-sm text-gray-600">
                <?= $cart_count ?> item<?= $cart_count !== 1 ? 's' : '' ?> in cart
            </div>
        </div>

        <?php if (empty($cart_items)): ?>
            <!-- Empty Cart -->
            <div class="empty-cart text-center py-16 bg-white rounded-lg shadow">
                <i class="fas fa-shopping-cart fa-4x text-gray-300 mb-4"></i>
                <h2 class="text-xl font-semibold text-gray-600 mb-2">Your cart is empty</h2>
                <p class="text-gray-500 mb-6">Add some products to get started!</p>
                <a href="index.php?module=products" 
                   class="inline-block bg-theme-red hover:bg-theme-red-dark text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    <i class="fas fa-shopping-bag mr-2"></i>Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <!-- Cart Items -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Cart Items List -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-4 border-b bg-gray-50">
                            <h3 class="font-semibold text-gray-800">Cart Items</h3>
                        </div>
                        
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($cart_items as $item): ?>
                                <?php
                                // Validate item has required fields
                                if (!isset($item['id']) || !isset($item['name']) || !isset($item['price']) || !isset($item['quantity'])) {
                                    continue; // Skip invalid items
                                }
                                
                                // Set defaults for optional fields
                                $image_url = $item['image_url'] ?? 'default.jpg';
                                $stock_quantity = $item['stock_quantity'] ?? 0;
                                ?>
                                <div class="p-4 flex items-center space-x-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        <img src="assets/images/products/<?= htmlspecialchars($image_url) ?>" 
                                             alt="<?= htmlspecialchars($item['name']) ?>"
                                             class="w-16 h-16 object-cover rounded-md">
                                    </div>
                                    
                                    <!-- Product Info -->
                                    <div class="flex-grow">
                                        <h4 class="font-medium text-gray-800 mb-1">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </h4>
                                        <p class="text-sm text-gray-500 mb-2">
                                            <?= formatProductPrice($item['price']) ?> each
                                        </p>
                                        <div class="text-xs text-gray-400">
                                            Stock: <?= $stock_quantity ?> available
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center space-x-2">
                                        <form method="POST" action="index.php?module=cart&action=update" class="flex items-center space-x-2">
                                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                            <button type="button" onclick="decreaseQuantity(<?= $item['id'] ?>)" 
                                                    class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center text-sm">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" name="quantity" id="qty-<?= $item['id'] ?>" 
                                                   value="<?= $item['quantity'] ?>" min="1" max="<?= max(1, $stock_quantity) ?>"
                                                   class="w-16 text-center border border-gray-300 rounded px-2 py-1 text-sm"
                                                   onchange="updateCartQuantity(<?= $item['id'] ?>, this.value)">
                                            <button type="button" onclick="increaseQuantity(<?= $item['id'] ?>)" 
                                                    class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center text-sm">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Item Total -->
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-800">
                                            <?= formatProductPrice($item['price'] * $item['quantity']) ?>
                                        </div>
                                        <button onclick="removeFromCart(<?= $item['id'] ?>)" 
                                                class="text-red-600 hover:text-red-800 text-sm mt-1">
                                            <i class="fas fa-trash-alt mr-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Cart Actions -->
                        <div class="p-4 bg-gray-50 border-t">
                            <div class="flex justify-between items-center">
                                <button onclick="clearCart()" 
                                        class="text-gray-600 hover:text-gray-800 text-sm">
                                    <i class="fas fa-trash mr-1"></i>Clear Cart
                                </button>
                                <a href="index.php?module=products" 
                                   class="text-theme-red hover:text-theme-red-dark text-sm font-medium">
                                    <i class="fas fa-arrow-left mr-1"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow overflow-hidden sticky top-4">
                        <div class="p-4 border-b bg-gray-50">
                            <h3 class="font-semibold text-gray-800">Order Summary</h3>
                        </div>
                        
                        <div class="p-4 space-y-4">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal (<?= $cart_count ?> items)</span>
                                <span><?= formatProductPrice($cart_total) ?></span>
                            </div>
                            
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Shipping</span>
                                <span>FREE within Klang Valley</span>
                            </div>
                            
                            <hr class="border-gray-200">
                            
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-theme-red"><?= formatProductPrice($cart_total) ?></span>
                            </div>
                            
                            <button onclick="proceedToCheckout()" 
                                    class="w-full bg-theme-red hover:bg-theme-red-dark text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                                <i class="fas fa-credit-card mr-2"></i>Proceed to Checkout
                            </button>
                            
                            <div class="text-xs text-gray-500 text-center">
                                <i class="fas fa-shield-alt mr-1"></i>Secure checkout with 256-bit SSL encryption
                            </div>
                        </div>
                        
                        <!-- Payment Methods -->
                        <div class="p-4 border-t bg-gray-50">
                            <div class="text-xs text-gray-600 mb-2">We accept:</div>
                            <div class="flex space-x-2 text-xs">
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">FPX</span>
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded">Touch n Go</span>
                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded">Boost</span>
                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded">Cards</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function decreaseQuantity(productId) {
    const input = document.getElementById('qty-' + productId);
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
        updateCartQuantity(productId, input.value);
    }
}

function increaseQuantity(productId) {
    const input = document.getElementById('qty-' + productId);
    const currentValue = parseInt(input.value);
    const maxValue = parseInt(input.max);
    if (currentValue < maxValue) {
        input.value = currentValue + 1;
        updateCartQuantity(productId, input.value);
    }
}

function updateCartQuantity(productId, quantity) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?module=cart&action=update';
    
    const productIdInput = document.createElement('input');
    productIdInput.type = 'hidden';
    productIdInput.name = 'product_id';
    productIdInput.value = productId;
    
    const quantityInput = document.createElement('input');
    quantityInput.type = 'hidden';
    quantityInput.name = 'quantity';
    quantityInput.value = quantity;
    
    form.appendChild(productIdInput);
    form.appendChild(quantityInput);
    document.body.appendChild(form);
    form.submit();
}

function removeFromCart(productId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        window.location.href = 'index.php?module=cart&action=remove&product_id=' + productId;
    }
}

function clearCart() {
    if (confirm('Are you sure you want to clear your entire cart?')) {
        window.location.href = 'index.php?module=cart&action=clear';
    }
}

function proceedToCheckout() {
    window.location.href = 'index.php?module=payments&action=checkout';
}
</script> 