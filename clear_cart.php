<?php
// Clear cart script
session_start();

// Clear all cart data
unset($_SESSION['cart']);
unset($_SESSION['cart_session_id']);

// Clear any error messages
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);

echo "Cart cleared successfully. <a href='index.php?module=cart'>Go to cart</a>";
?> 