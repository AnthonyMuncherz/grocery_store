<?php
// Turn off error display
ini_set('display_errors', 0);
error_reporting(0);

echo "Error display turned off.<br>";
echo "Current display_errors: " . ini_get('display_errors') . "<br>";
echo "Current error_reporting: " . error_reporting() . "<br>";
echo "<a href='clear_cart.php'>Clear cart</a> | <a href='index.php?module=cart'>Go to cart</a>";
?> 