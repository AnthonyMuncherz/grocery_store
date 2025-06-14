/**
 * Cart JavaScript Functions
 * Handles AJAX cart operations and user interactions
 */

// Add to cart function with AJAX
function addToCart(productId, quantity = 1) {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
    button.disabled = true;
    
    // Create form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    // Send AJAX request
    fetch('index.php?module=cart&action=add', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            // Show success notification
            showNotification('Product added to cart successfully!', 'success');
            
            // Update cart count in header
            updateCartCount();
            
            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
            
            // Optional: Redirect to cart after a short delay
            setTimeout(() => {
                window.location.href = 'index.php?module=cart';
            }, 1500);
        } else {
            throw new Error('Network response was not ok');
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showNotification('Error adding product to cart. Please try again.', 'error');
        
        // Reset button
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Add to cart with custom quantity from product view
function addToCartWithQuantity(productId) {
    const quantityInput = document.getElementById('product-quantity');
    const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
    
    if (quantity < 1) {
        showNotification('Please select a valid quantity.', 'error');
        return;
    }
    
    addToCart(productId, quantity);
}

// Update cart item quantity
function updateCartItem(productId, quantity) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    fetch('index.php?module=cart&action=update', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            // Reload page to show updated cart
            window.location.reload();
        } else {
            throw new Error('Network response was not ok');
        }
    })
    .catch(error => {
        console.error('Error updating cart:', error);
        showNotification('Error updating cart. Please try again.', 'error');
    });
}

// Show notification function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
    
    // Set notification type styles
    switch (type) {
        case 'success':
            notification.classList.add('bg-green-500', 'text-white');
            break;
        case 'error':
            notification.classList.add('bg-red-500', 'text-white');
            break;
        case 'warning':
            notification.classList.add('bg-yellow-500', 'text-white');
            break;
        default:
            notification.classList.add('bg-blue-500', 'text-white');
    }
    
    // Set notification content
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Update cart count in header
function updateCartCount() {
    fetch('api/cart.php?action=count')
        .then(response => response.json())
        .then(data => {
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement && data.count !== undefined) {
                cartCountElement.textContent = data.count;
                
                // Show/hide cart count badge
                if (data.count > 0) {
                    cartCountElement.style.display = 'inline-block';
                } else {
                    cartCountElement.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error updating cart count:', error);
        });
}

// Initialize cart functionality when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count on page load
    updateCartCount();
    
    // Add event listeners for quantity inputs
    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.closest('form').querySelector('input[name="product_id"]').value;
            const quantity = parseInt(this.value);
            
            if (quantity > 0) {
                updateCartItem(productId, quantity);
            }
        });
    });
});

// Product view quantity controls
function increaseProductQuantity() {
    const input = document.getElementById('product-quantity');
    if (input) {
        const currentValue = parseInt(input.value);
        const maxValue = parseInt(input.max);
        if (currentValue < maxValue) {
            input.value = currentValue + 1;
        }
    }
}

function decreaseProductQuantity() {
    const input = document.getElementById('product-quantity');
    if (input) {
        const currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
        }
    }
} 