<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../functions.php';

$product = null;
if (isset($_GET['id'])) {
    $product = getProductWithSKU($_GET['id']);
}

if (!$product) {
    echo "<div class='p-6 text-red-600'>Product not found.</div>";
    return;
}

// Generate barcode for SKU
$barcodeSVG = generateBarcodeForSKU($product['sku'], 'svg', 300, 60);
$barcodePNG = generateBarcodeForSKU($product['sku'], 'png', 300, 60);
?>

<div class="max-w-6xl mx-auto mt-8 p-6 bg-white shadow-lg rounded-lg">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-red-600">Product Details</h1>
        <div class="flex space-x-3">
            <button onclick="printBarcode()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10V9z"></path>
                </svg>
                Print Barcode
            </button>
            <a href="/grocery_store/index.php?module=inventory&action=edit&id=<?= $product['id'] ?>" 
               class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Product
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Information -->
        <div class="space-y-6">
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Product Information</h2>
                <table class="w-full">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 w-1/3">Name</th>
                        <td class="px-4 py-3 text-gray-900"><?= htmlspecialchars($product['name']) ?></td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">SKU</th>
                        <td class="px-4 py-3">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-mono font-semibold">
                                <?= htmlspecialchars($product['sku']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Category</th>
                        <td class="px-4 py-3 text-gray-900"><?= htmlspecialchars($product['category_id']) ?></td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Price</th>
                        <td class="px-4 py-3">
                            <span class="text-lg font-semibold text-green-600">RM <?= number_format($product['price'], 2) ?></span>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Stock Quantity</th>
                        <td class="px-4 py-3">
                            <span class="<?= $product['stock_quantity'] < 10 ? 'text-red-600 font-bold' : 'text-gray-900' ?>">
                                <?= htmlspecialchars($product['stock_quantity']) ?> units
                            </span>
                            <?php if ($product['stock_quantity'] < 10): ?>
                                <span class="text-xs text-red-500 block">Low Stock Alert!</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if (!empty($product['description'])): ?>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Description</th>
                        <td class="px-4 py-3 text-gray-900"><?= htmlspecialchars($product['description']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($product['image_url'])): ?>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Image</th>
                        <td class="px-4 py-3">
                            <img src="/grocery_store/<?= htmlspecialchars($product['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 class="w-24 h-24 object-cover rounded-lg shadow-md">
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Barcode Section -->
        <div class="space-y-6">
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Product Barcode</h2>
                <div class="text-center space-y-4">
                    <div class="bg-white p-4 rounded-lg border-2 border-gray-200 inline-block">
                        <?= $barcodeSVG ?>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p class="font-mono font-semibold"><?= htmlspecialchars($product['sku']) ?></p>
                        <p class="text-xs">Code 128B Format</p>
                    </div>
                </div>
                
                <!-- Download Options -->
                <div class="mt-6 space-y-2">
                    <button onclick="downloadBarcode('svg')" 
                            class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download SVG
                    </button>
                    <button onclick="downloadBarcode('png')" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PNG
                    </button>
                </div>
            </div>

            <!-- Stock Management -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Quick Stock Update</h3>
                <form action="/grocery_store/index.php?module=inventory&action=update_stock" method="POST" class="space-y-3">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="flex space-x-2">
                        <input type="number" name="quantity" placeholder="Qty" 
                               class="flex-1 border border-yellow-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <select name="action" class="border border-yellow-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="add">Add Stock</option>
                            <option value="remove">Remove Stock</option>
                        </select>
                    </div>
                    <input type="text" name="notes" placeholder="Notes (optional)" 
                           class="w-full border border-yellow-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                        Update Stock
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="mt-8 flex justify-between">
        <a href="/grocery_store/index.php?module=inventory" 
           class="bg-gray-600 text-white px-6 py-3 rounded hover:bg-gray-700 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Inventory
        </a>
        
        <div class="text-sm text-gray-500">
            <p>Created: <?= date('M j, Y g:i A', strtotime($product['created_at'])) ?></p>
            <?php if ($product['updated_at'] !== $product['created_at']): ?>
                <p>Last Updated: <?= date('M j, Y g:i A', strtotime($product['updated_at'])) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<script>
const productData = {
    id: <?= $product['id'] ?>,
    name: <?= json_encode($product['name']) ?>,
    sku: <?= json_encode($product['sku']) ?>,
    barcodePNG: <?= json_encode($barcodePNG) ?>,
    barcodeSVG: <?= json_encode($barcodeSVG) ?>
};

function printBarcode() {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Barcode - ${productData.sku}</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; margin: 20px; }
                .barcode-container { margin: 20px auto; display: inline-block; }
                .product-info { margin: 10px 0; }
                h1 { color: #333; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <h1>${productData.name}</h1>
            <div class="barcode-container">
                ${productData.barcodeSVG}
            </div>
            <div class="product-info">
                <strong>SKU: ${productData.sku}</strong><br>
                Generated: ${new Date().toLocaleDateString()}
            </div>
            <scr' + 'ipt>
                window.onload = function() {
                    window.print();
                    window.onafterprint = function() {
                        window.close();
                    }
                }
            </scr' + 'ipt>
        </body>
        </html>
    `);
    printWindow.document.close();
}

function downloadBarcode(format) {
    if (format === 'svg') {
        const blob = new Blob([productData.barcodeSVG], { type: 'image/svg+xml' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${productData.sku}_barcode.svg`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    } else if (format === 'png') {
        const a = document.createElement('a');
        a.href = 'data:image/png;base64,' + productData.barcodePNG;
        a.download = `${productData.sku}_barcode.png`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
}
</script>
