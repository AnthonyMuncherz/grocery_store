<?php
/**
 * Payment Success Template
 * Variable available: $order (array|null)
 */
?>
<div class="success-container py-6">
    <div class="max-w-3xl mx-auto px-4 text-center bg-white shadow rounded-lg p-8">
        <?php if ($order): ?>
            <h1 class="text-2xl font-bold text-green-600 mb-4 flex items-center justify-center">
                <i class="fas fa-check-circle mr-2"></i>Payment Successful!
            </h1>
            <p class="text-gray-700 mb-6">Thank you, <?= htmlspecialchars($order['customer_name']) ?>. Your order has been placed successfully.</p>

            <!-- Order Details -->
            <div class="text-left space-y-2 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="font-medium">Order ID:</span>
                    <span><?= htmlspecialchars($order['id']) ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="font-medium">Payment Method:</span>
                    <span><?= htmlspecialchars($order['payment_method']) ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="font-medium">Total Paid:</span>
                    <span><?= formatMYR($order['total_amount']) ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="font-medium">Status:</span>
                    <span class="text-green-600 font-semibold"><?= htmlspecialchars($order['status']) ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="font-medium">Date &amp; Time:</span>
                    <span><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
                </div>
            </div>

            <a href="index.php?module=orders&action=view&id=<?= $order['id'] ?>" class="inline-block bg-theme-red hover:bg-theme-red-dark text-white font-semibold py-3 px-6 rounded-lg transition-colors mr-2">
                <i class="fas fa-receipt mr-2"></i>View Order
            </a>
            <a href="index.php?module=products" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-colors">
                <i class="fas fa-shopping-basket mr-2"></i>Continue Shopping
            </a>
        <?php else: ?>
            <h1 class="text-2xl font-bold text-red-600 mb-4 flex items-center justify-center">
                <i class="fas fa-times-circle mr-2"></i>Order Not Found
            </h1>
            <p class="text-gray-700 mb-6">We could not find your order details. If you believe this is an error, please contact support.</p>
            <a href="index.php" class="inline-block bg-theme-red hover:bg-theme-red-dark text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                <i class="fas fa-home mr-2"></i>Return to Home
            </a>
        <?php endif; ?>
    </div>
</div> 