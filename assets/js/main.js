// Main JavaScript file for Malaysian Grocery Store

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize alerts
    var alertList = document.querySelectorAll('.alert');
    alertList.forEach(function (alert) {
        new bootstrap.Alert(alert);
    });

    // Cart quantity handlers
    var quantityInputs = document.querySelectorAll('.cart-quantity');
    quantityInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            updateCartItem(this.dataset.productId, this.value);
        });
    });

    // Payment method selection
    var paymentMethods = document.querySelectorAll('.payment-method-card');
    paymentMethods.forEach(function(method) {
        method.addEventListener('click', function() {
            // Remove selected class from all methods
            paymentMethods.forEach(m => m.classList.remove('selected'));
            // Add selected class to clicked method
            this.classList.add('selected');
            // Update hidden input
            document.getElementById('selected_payment_method').value = this.dataset.method;
        });
    });
});

// Update cart item quantity
function updateCartItem(productId, quantity) {
    if (quantity < 1) {
        if (confirm('Remove item from cart?')) {
            quantity = 0;
        } else {
            document.querySelector(`[data-product-id="${productId}"]`).value = 1;
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
            if (quantity === 0) {
                removeCartItem(productId);
            }
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the cart.');
    });
}

// Update cart total display
function updateCartTotal(total) {
    document.getElementById('cart-total').textContent = total;
}

// Update cart count in header
function updateCartCount(count) {
    const cartBadge = document.querySelector('.cart-badge');
    if (cartBadge) {
        cartBadge.textContent = count;
        cartBadge.style.display = count > 0 ? 'inline' : 'none';
    }
}

// Remove cart item from DOM
function removeCartItem(productId) {
    const item = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
    if (item) {
        item.remove();
    }
}

// Format currency in MYR
function formatMYR(amount) {
    return 'RM ' + parseFloat(amount).toFixed(2);
}

// Validate Malaysian phone number
function validateMYPhone(phone) {
    const regex = /^\+60\s\d{2}-\d{7,8}$/;
    return regex.test(phone);
}

// Show loading spinner
function showLoading(buttonElement) {
    const originalText = buttonElement.innerHTML;
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<span class="loading-spinner"></span> Loading...';
    return originalText;
}

// Hide loading spinner
function hideLoading(buttonElement, originalText) {
    buttonElement.disabled = false;
    buttonElement.innerHTML = originalText;
}

// Handle form submission with loading state
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('[type="submit"]');
        if (submitBtn) {
            const originalText = showLoading(submitBtn);
            // Store original text in data attribute for reference
            submitBtn.dataset.originalText = originalText;
        }
    });
}); 