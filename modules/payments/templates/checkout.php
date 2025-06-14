<?php
/**
 * Checkout Template
 * Displays customer information form and payment method selection.
 * Variables available: $cart_items, $cart_total, $cart_count, $banks, $ewallets
 */
?>
<div class="checkout-container py-6">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-credit-card mr-2 text-theme-red"></i>Checkout
        </h1>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="mb-6 bg-red-100 border border-red-500 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold"><i class="fas fa-exclamation-circle mr-1"></i>Error:</strong>
                <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error_message']) ?></span>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php elseif (isset($_SESSION['success_message'])): ?>
            <div class="mb-6 bg-green-100 border border-green-500 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold"><i class="fas fa-check-circle mr-1"></i>Success:</strong>
                <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success_message']) ?></span>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?module=payments&action=process" method="POST" class="space-y-8 bg-white shadow rounded-lg p-6">
            <!-- Customer Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Customer Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1" for="name">Full Name</label>
                        <input required type="text" id="name" name="name" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red" placeholder="e.g. Ali bin Abu">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1" for="email">Email</label>
                        <input required type="email" id="email" name="email" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red" placeholder="e.g. ali@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1" for="phone">Phone Number</label>
                        <input required type="text" id="phone" name="phone" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red" placeholder="e.g. 0123456789">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1" for="address">Delivery Address</label>
                        <textarea required id="address" name="address" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red" placeholder="Street, City, State, Postcode"></textarea>
                    </div>
                </div>
            </div>

            <!-- Payment Method Selection -->
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Payment Method</h2>
                <div class="space-y-6">
                    <!-- FPX -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-4">
                            <input type="radio" id="pm_fpx" name="payment_method" value="fpx" class="h-4 w-4 text-theme-red focus:ring-theme-red" checked>
                            <label for="pm_fpx" class="ml-2 block text-sm font-medium text-gray-700">FPX Online Banking</label>
                        </div>
                        <div class="ml-6 grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="text-sm text-gray-600" for="fpx_bank">Select Bank</label>
                            <select name="fpx_bank" id="fpx_bank" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red md:col-span-1">
                                <?php foreach ($banks as $code => $name): ?>
                                    <option value="<?= htmlspecialchars($code) ?>"><?= htmlspecialchars($name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- E-Wallet -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-4">
                            <input type="radio" id="pm_ewallet" name="payment_method" value="ewallet" class="h-4 w-4 text-theme-red focus:ring-theme-red">
                            <label for="pm_ewallet" class="ml-2 block text-sm font-medium text-gray-700">E-Wallet</label>
                        </div>
                        <div class="ml-6 grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="text-sm text-gray-600" for="ewallet_provider">Provider</label>
                            <select name="ewallet_provider" id="ewallet_provider" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red">
                                <?php foreach ($ewallets as $code => $name): ?>
                                    <option value="<?= htmlspecialchars($code) ?>"><?= htmlspecialchars($name) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label class="text-sm text-gray-600 mt-2 md:mt-0" for="ewallet_phone">Wallet Phone No.</label>
                            <input type="text" name="ewallet_phone" id="ewallet_phone" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red" placeholder="e.g. 0123456789">
                        </div>
                    </div>

                    <!-- Credit / Debit Card -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-4">
                            <input type="radio" id="pm_card" name="payment_method" value="card" class="h-4 w-4 text-theme-red focus:ring-theme-red">
                            <label for="pm_card" class="ml-2 block text-sm font-medium text-gray-700">Credit / Debit Card</label>
                        </div>
                        <div class="ml-6 grid grid-cols-1 md:grid-cols-2 gap-2">
                            <label class="text-sm text-gray-600" for="card_number">Card Number</label>
                            <input type="text" name="card_number" id="card_number" maxlength="19" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red" placeholder="XXXX XXXX XXXX XXXX">
                            <label class="text-sm text-gray-600" for="expiry_month">Expiry (MM/YY)</label>
                            <div class="flex space-x-2">
                                <input type="text" name="expiry_month" id="expiry_month" maxlength="2" class="w-1/2 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red" placeholder="MM">
                                <input type="text" name="expiry_year" id="expiry_year" maxlength="2" class="w-1/2 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red" placeholder="YY">
                            </div>
                            <label class="text-sm text-gray-600" for="cvv">CVV</label>
                            <input type="text" name="cvv" id="cvv" maxlength="4" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red" placeholder="XXX">
                            <label class="text-sm text-gray-600 mt-2 md:mt-0" for="card_holder">Card Holder Name</label>
                            <input type="text" name="card_holder" id="card_holder" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-theme-red" placeholder="Name on card">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="flex justify-between text-sm mb-2">
                    <span>Items (<?= $cart_count ?>)</span>
                    <span><?= formatMYR($cart_total) ?></span>
                </div>
                <div class="flex justify-between text-lg font-bold mb-2">
                    <span>Total</span>
                    <span class="text-theme-red"><?= formatMYR($cart_total) ?></span>
                </div>
                <button type="submit" class="w-full bg-theme-red hover:bg-theme-red-dark text-white font-semibold py-3 rounded-lg transition-colors">
                    <i class="fas fa-lock mr-2"></i>Pay Securely
                </button>
            </div>
        </form>
    </div>
</div> 