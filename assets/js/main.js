// Main JavaScript file for Malaysian Grocery Store

document.addEventListener('DOMContentLoaded', function () {
    // Initialize tooltips (if you keep Bootstrap JS and tooltips)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Alerts initialization removed as per refactor note

    // Cart quantity handlers
    var quantityInputs = document.querySelectorAll('.cart-quantity');
    quantityInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            updateCartItem(this.dataset.productId, this.value);
        });
    });

    // Payment method selection
    var paymentMethods = document.querySelectorAll('.payment-method-card');
    paymentMethods.forEach(function (method) {
        method.addEventListener('click', function () {
            // Remove selected classes from all methods
            paymentMethods.forEach(m => {
                m.classList.remove('border-malaysia-blue', 'bg-blue-50', 'shadow-lg'); // Tailwind selected classes
                m.classList.add('border-gray-300'); // Default border
            });
            // Add selected classes to clicked method
            this.classList.add('border-malaysia-blue', 'bg-blue-50', 'shadow-lg');
            this.classList.remove('border-gray-300');
            // Update hidden input
            const selectedPaymentInput = document.getElementById('selected_payment_method');
            if (selectedPaymentInput) {
                selectedPaymentInput.value = this.dataset.method;
            }
        });
    });

    // Mobile menu toggle (moved to footer.php for direct script tag)

    // Scroll-triggered animations for the home page
    const animatedElements = document.querySelectorAll('.animate-on-scroll');

    if (animatedElements.length > 0) {
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = parseInt(entry.target.dataset.animationDelay || 0, 10);
                    setTimeout(() => {
                        entry.target.classList.add('is-visible');
                        // For elements animated with initial Tailwind classes like opacity-0:
                        if (entry.target.classList.contains('animate-fade-in-up')) {
                            // These classes are defined in an inline style block in main.php or in style.css
                            // The .is-visible class will trigger the transition
                        }
                        // Or, if you prefer to remove Tailwind classes:
                        // entry.target.classList.remove('opacity-0', 'translate-y-10'); // Example
                    }, delay);
                    observer.unobserve(entry.target); // Animate only once
                }
            });
        }, { threshold: 0.1 }); // Trigger when 10% of the element is visible

        animatedElements.forEach(el => {
            observer.observe(el);
        });
    }
});

// Update cart item quantity
function updateCartItem(productId, quantity) {
    // Ensure quantity is a number
    const numQuantity = parseInt(quantity, 10);

    if (numQuantity < 1) {
        if (confirm('Remove item from cart?')) {
            quantity = 0;
        } else {
            // Reset the input value to its previous state or 1 if not available
            const inputElement = document.querySelector(`.cart-item input[data-product-id="${productId}"]`);
            if (inputElement) {
                // Ideally, store previous value or fetch it. For simplicity, reset to 1 or current cart state.
                // This part might need more robust state management if user cancels removal of a 0-quantity item.
                // For now, just ensure it's not submitted as negative.
                // The server side should also validate.
                inputElement.value = 1; // Or fetch current quantity again
            }
            return;
        }
    }

    fetch('index.php?module=orders&action=update_cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&quantity=${quantity}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateCartTotal(data.total);
                updateCartCount(data.count);
                if (parseInt(quantity, 10) === 0) {
                    removeCartItemDOM(productId); // Renamed for clarity
                }
                // Optionally, display a success message
                // For example, using a simple alert or a more sophisticated notification system
                // alert('Cart updated successfully!');
            } else {
                alert(data.message || 'Failed to update cart.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the cart.');
        });
}

// Update cart total display
function updateCartTotal(total) {
    const cartTotalElement = document.getElementById('cart-total');
    if (cartTotalElement) {
        cartTotalElement.textContent = formatMYR(total); // Assuming formatMYR is available
    }
}

// Update cart count in header
function updateCartCount(count) {
    const cartBadge = document.querySelector('.cart-badge');
    if (cartBadge) {
        cartBadge.textContent = count;
        cartBadge.style.display = count > 0 ? 'flex' : 'none'; // 'flex' for centered text in badge
    }
}

// Remove cart item from DOM
function removeCartItemDOM(productId) {
    // Assuming cart items have a unique class or data attribute structure
    // e.g., <div class="cart-item-row" data-product-id="123">...</div>
    const itemRow = document.querySelector(`.cart-item-row[data-product-id="${productId}"]`);
    if (itemRow) {
        itemRow.remove();
    }
    // Check if cart is empty to display a message
    const cartItemsContainer = document.getElementById('cart-items-container'); // Assuming you have such a container
    if (cartItemsContainer && cartItemsContainer.children.length === 0) {
        cartItemsContainer.innerHTML = '<p class="text-gray-600">Your cart is empty.</p>';
        // Also hide checkout button or cart summary if needed
        const checkoutButton = document.getElementById('checkout-button');
        if (checkoutButton) checkoutButton.style.display = 'none';
    }
}

// Format currency in MYR
function formatMYR(amount) {
    return 'RM ' + parseFloat(amount).toFixed(2);
}

// Validate Malaysian phone number
function validateMYPhone(phone) {
    // Example regex, adjust as needed for your specific validation rules
    const regex = /^\+?60\s?\d{1,2}[-\s]?\d{7,8}$/; // Allows +60, optional space, 1-2 digits, optional dash/space, 7-8 digits
    return regex.test(phone);
}

// Show loading spinner
function showLoading(buttonElement) {
    const originalText = buttonElement.innerHTML;
    buttonElement.disabled = true;
    // Tailwind spinner:
    buttonElement.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span> Loading...';
    buttonElement.dataset.originalText = originalText; // Store original text
    return originalText; // Kept for compatibility if any part of code uses it
}

// Hide loading spinner
function hideLoading(buttonElement, originalText) {
    buttonElement.disabled = false;
    buttonElement.innerHTML = originalText || buttonElement.dataset.originalText || 'Submit'; // Use stored or default
}

// Handle form submission with loading state
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function (e) {
        // Check if this form is for AJAX submission or a full page reload.
        // For full page reloads, the loading state might be brief or reset by navigation.
        // This is more useful for AJAX forms.
        const submitBtn = this.querySelector('[type="submit"]');
        if (submitBtn && !submitBtn.classList.contains('no-loading')) { // Add 'no-loading' class to buttons to skip this
            showLoading(submitBtn);
            // For non-AJAX forms, the loading state will disappear upon page navigation.
            // If it's an AJAX form, you'd call hideLoading in the .then() or .finally() of your fetch.
        }
    });
});