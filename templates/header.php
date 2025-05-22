<?php
require_once __DIR__ . '/../includes/constants.php';

// Determine alert classes based on session message type
// These colors are for system messages (success, error, etc.) and should typically remain distinct from the main theme.
$alert_bg_color = 'bg-blue-100';
$alert_border_color = 'border-blue-500';
$alert_text_color = 'text-blue-700';
$icon_class = 'fa-info-circle'; // Default icon

if (isset($_SESSION['message_type'])) {
    switch ($_SESSION['message_type']) {
        case 'success':
            $alert_bg_color = 'bg-green-100';
            $alert_border_color = 'border-green-500';
            $alert_text_color = 'text-green-700';
            $icon_class = 'fa-check-circle';
            break;
        case 'warning':
            $alert_bg_color = 'bg-yellow-100';
            $alert_border_color = 'border-yellow-500';
            $alert_text_color = 'text-yellow-700';
            $icon_class = 'fa-exclamation-triangle';
            break;
        case 'danger':
        case 'error':
            $alert_bg_color = 'bg-red-100';
            $alert_border_color = 'border-red-500';
            $alert_text_color = 'text-red-700';
            $icon_class = 'fa-exclamation-circle';
            break;
    }
}

// Determine current module for active nav link
$current_module = $_GET['module'] ?? 'home'; // Default to 'home' if not set
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'theme-red': '#DC2626', // Example: Tailwind red-600
                        'theme-red-dark': '#B91C1C', // Example: Tailwind red-700/800
                        'theme-red-light': '#FEE2E2', // Example: Tailwind red-100
                        // Keep old blue for compatibility or specific elements if needed, or remove
                        'malaysia-blue': '#0052B4',
                        'malaysia-blue-dark': '#003D87',
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles (minimal, if any, after Tailwind integration) -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
    <nav class="bg-theme-red shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a class="text-white font-bold text-xl" href="index.php"><?php echo APP_NAME; ?></a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a class="text-gray-300 hover:bg-theme-red-dark hover:text-white px-3 py-2 rounded-md text-sm font-medium <?php echo $current_module === 'home' ? 'bg-theme-red-dark text-white' : ''; ?>"
                            href="index.php">
                            <i class="fas fa-home mr-1"></i> Home
                        </a>
                        <a class="text-gray-300 hover:bg-theme-red-dark hover:text-white px-3 py-2 rounded-md text-sm font-medium <?php echo $current_module === 'products' ? 'bg-theme-red-dark text-white' : ''; ?>"
                            href="index.php?module=products">
                            <i class="fas fa-shopping-basket mr-1"></i> Products
                        </a>
                        <a class="text-gray-300 hover:bg-theme-red-dark hover:text-white px-3 py-2 rounded-md text-sm font-medium <?php echo $current_module === 'orders' ? 'bg-theme-red-dark text-white' : ''; ?>"
                            href="index.php?module=orders">
                            <i class="fas fa-receipt mr-1"></i> Orders
                        </a>
                        <a class="text-gray-300 hover:bg-theme-red-dark hover:text-white px-3 py-2 rounded-md text-sm font-medium <?php echo $current_module === 'inventory' ? 'bg-theme-red-dark text-white' : ''; ?>"
                            href="index.php?module=inventory">
                            <i class="fas fa-boxes mr-1"></i> Inventory
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <a href="index.php?module=orders&action=view_cart"
                        class="text-gray-300 hover:bg-theme-red-dark hover:text-white px-3 py-2 rounded-md text-sm font-medium relative">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                            <span
                                class="cart-badge absolute -top-2 -right-2 bg-yellow-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $_SESSION['cart_count']; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <button type="button"
                        class="bg-theme-red-dark inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="index.php"
                    class="text-gray-300 hover:bg-theme-red-dark hover:text-white block px-3 py-2 rounded-md text-base font-medium <?php echo $current_module === 'home' ? 'bg-theme-red-dark text-white' : ''; ?>">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                <a href="index.php?module=products"
                    class="text-gray-300 hover:bg-theme-red-dark hover:text-white block px-3 py-2 rounded-md text-base font-medium <?php echo $current_module === 'products' ? 'bg-theme-red-dark text-white' : ''; ?>">
                    <i class="fas fa-shopping-basket mr-2"></i>Products
                </a>
                <a href="index.php?module=orders"
                    class="text-gray-300 hover:bg-theme-red-dark hover:text-white block px-3 py-2 rounded-md text-base font-medium <?php echo $current_module === 'orders' ? 'bg-theme-red-dark text-white' : ''; ?>">
                    <i class="fas fa-receipt mr-2"></i>Orders
                </a>
                <a href="index.php?module=inventory"
                    class="text-gray-300 hover:bg-theme-red-dark hover:text-white block px-3 py-2 rounded-md text-base font-medium <?php echo $current_module === 'inventory' ? 'bg-theme-red-dark text-white' : ''; ?>">
                    <i class="fas fa-boxes mr-2"></i>Inventory
                </a>
                <a href="index.php?module=orders&action=view_cart"
                    class="text-gray-300 hover:bg-theme-red-dark hover:text-white block px-3 py-2 rounded-md text-base font-medium relative">
                    <i class="fas fa-shopping-cart mr-2"></i>Cart
                    <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                        <span
                            class="cart-badge ml-2 inline-block bg-yellow-500 text-white text-xs rounded-full h-5 px-1.5 py-0.5 items-center justify-center"><?php echo $_SESSION['cart_count']; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-4 flex-grow">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $alert_bg_color . ' ' . $alert_border_color . ' ' . $alert_text_color; ?> border-l-4 p-4 mb-4 relative"
                role="alert">
                <p class="font-bold"><i
                        class="fas <?php echo $icon_class; ?> mr-2"></i><?php echo ucfirst($_SESSION['message_type'] ?? 'Notification'); ?>
                </p>
                <p><?php echo $_SESSION['message']; ?></p>
                <?php
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-bs-dismiss="alert"
                    aria-label="Close">
                    <span class="text-2xl <?php echo $alert_text_color; ?> opacity-75 hover:opacity-100">&times;</span>
                </button>
            </div>
        <?php endif; ?>