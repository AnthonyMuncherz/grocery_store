<?php
/**
 * Order View Template - Display Single Order Details
 */
?>

<style>
    .order-view-container {
        max-width: 900px;
        margin: 20px auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e1e8ed;
    }

    .order-header h2 {
        color: #2c3e50;
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .back-btn {
        background: #6c757d;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .back-btn:hover {
        background: #5a6268;
        text-decoration: none;
        color: white;
    }

    .order-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .info-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #3498db;
    }

    .info-section h3 {
        color: #2c3e50;
        margin: 0 0 15px 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .info-item {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .info-label {
        font-weight: 600;
        color: #34495e;
        margin-right: 10px;
    }

    .info-value {
        color: #2c3e50;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-shipped { background: #d4edda; color: #155724; }
    .status-delivered { background: #d1ecf1; color: #0c5460; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    .order-amount {
        font-weight: 700;
        color: #27ae60;
        font-size: 1.2rem;
    }

    .order-items-section {
        margin-top: 30px;
    }

    .order-items-section h3 {
        color: #2c3e50;
        margin-bottom: 20px;
        font-size: 1.3rem;
        font-weight: 600;
        border-bottom: 2px solid #e1e8ed;
        padding-bottom: 10px;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .items-table th,
    .items-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #e1e8ed;
    }

    .items-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #2c3e50;
    }

    .items-table tr:hover {
        background: #f8f9fa;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .product-image {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #ddd;
    }

    .product-name {
        font-weight: 600;
        color: #2c3e50;
    }

    .item-total {
        font-weight: 600;
        color: #27ae60;
    }

    .actions-section {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #e1e8ed;
        display: flex;
        gap: 15px;
    }

    .action-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .edit-btn {
        background: #f39c12;
        color: white;
    }

    .edit-btn:hover {
        background: #d68910;
        color: white;
        text-decoration: none;
    }

    .print-btn {
        background: #17a2b8;
        color: white;
    }

    .print-btn:hover {
        background: #138496;
        color: white;
        text-decoration: none;
    }

    .delete-btn {
        background: #dc3545;
        color: white;
    }

    .delete-btn:hover {
        background: #c82333;
        color: white;
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .order-info-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .order-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        
        .items-table {
            font-size: 0.9rem;
        }
        
        .items-table th,
        .items-table td {
            padding: 10px 8px;
        }
        
        .actions-section {
            flex-direction: column;
        }
    }
</style>

<div class="order-view-container">
    <div class="order-header">
        <h2>📄 Order #<?= htmlspecialchars($order['id']) ?></h2>
        <a href="index.php?module=orders&action=list" class="back-btn">← Back to Orders</a>
    </div>

    <!-- Order Information Grid -->
    <div class="order-info-grid">
        <!-- Customer Information -->
        <div class="info-section">
            <h3>👤 Customer Information</h3>
            <div class="info-item">
                <span class="info-label">Name:</span>
                <span class="info-value"><?= htmlspecialchars($order['customer_name']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Email:</span>
                <span class="info-value"><?= htmlspecialchars($order['customer_email']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Phone:</span>
                <span class="info-value"><?= htmlspecialchars($order['customer_phone']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Address:</span>
                <span class="info-value"><?= htmlspecialchars($order['shipping_address']) ?></span>
            </div>
        </div>

        <!-- Order Details -->
        <div class="info-section">
            <h3>📋 Order Details</h3>
            <div class="info-item">
                <span class="info-label">Order ID:</span>
                <span class="info-value">#<?= htmlspecialchars($order['id']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
                    <?= ucfirst(htmlspecialchars($order['status'])) ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Payment Method:</span>
                <span class="info-value"><?= htmlspecialchars($order['payment_method']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Amount:</span>
                <span class="order-amount">RM <?= number_format($order['total_amount'], 2) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Order Date:</span>
                <span class="info-value"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
            </div>
            <?php if ($order['updated_at'] !== $order['created_at']): ?>
            <div class="info-item">
                <span class="info-label">Last Updated:</span>
                <span class="info-value"><?= date('M j, Y g:i A', strtotime($order['updated_at'])) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order Items -->
    <div class="order-items-section">
        <h3>🛒 Order Items (<?= count($order['items']) ?> items)</h3>
        
        <?php if (!empty($order['items'])): ?>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                             alt="<?= htmlspecialchars($item['product_name'] ?? 'Product') ?>" 
                                             class="product-image">
                                    <?php else: ?>
                                        <div class="product-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #666;">📦</div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="product-name">
                                            <?= htmlspecialchars($item['product_name'] ?? 'Unknown Product') ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: #666;">
                                            Product ID: <?= htmlspecialchars($item['product_id']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>RM <?= number_format($item['unit_price'], 2) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td class="item-total">
                                RM <?= number_format($item['unit_price'] * $item['quantity'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="border-top: 2px solid #e1e8ed; background: #f8f9fa;">
                        <td colspan="3" style="text-align: right; font-weight: 600; font-size: 1.1rem;">
                            <strong>Grand Total:</strong>
                        </td>
                        <td class="order-amount" style="font-size: 1.2rem;">
                            <strong>RM <?= number_format($order['total_amount'], 2) ?></strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <p>No items found for this order.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Actions -->
    <div class="actions-section">
        <a href="index.php?module=orders&action=edit&id=<?= $order['id'] ?>" class="action-btn edit-btn">✏️ Edit Order</a>
        <button onclick="printOrder()" class="action-btn print-btn">🖨️ Print Order</button>
        <button onclick="confirmDelete(<?= $order['id'] ?>)" class="action-btn delete-btn">🗑️ Delete Order</button>
    </div>
</div>

<script>
    function confirmDelete(orderId) {
        if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
            window.location.href = `index.php?module=orders&action=delete&id=${orderId}`;
        }
    }

    // Enhanced print functionality
    function printOrder() {
        // Create a print window with enhanced styling
        const printWindow = window.open('', '_blank');
        const orderContent = document.querySelector('.order-view-container').cloneNode(true);
        
        // Remove action buttons and back button for print
        const actionsSection = orderContent.querySelector('.actions-section');
        const backBtn = orderContent.querySelector('.back-btn');
        if (actionsSection) actionsSection.remove();
        if (backBtn) backBtn.remove();
        
        // Enhanced print styles
        const printStyles = `
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 0; 
                    padding: 20px;
                    font-size: 12px;
                    line-height: 1.4;
                }
                .order-view-container { 
                    box-shadow: none !important; 
                    margin: 0;
                    padding: 0;
                }
                .order-header {
                    border-bottom: 2px solid #000;
                    margin-bottom: 20px;
                    padding-bottom: 10px;
                }
                .order-header h2 {
                    margin: 0;
                    color: #000;
                }
                .info-section {
                    background: none !important;
                    border: 1px solid #ccc;
                    margin-bottom: 15px;
                    page-break-inside: avoid;
                }
                .items-table {
                    border: 1px solid #000;
                    page-break-inside: avoid;
                }
                .items-table th,
                .items-table td {
                    border: 1px solid #ccc;
                    padding: 8px;
                }
                .items-table th {
                    background: #f0f0f0 !important;
                    font-weight: bold;
                }
                .status-badge {
                    background: none !important;
                    color: #000 !important;
                    border: 1px solid #000;
                    padding: 2px 8px;
                }
                .order-amount {
                    color: #000 !important;
                    font-weight: bold;
                }
                .product-image {
                    display: none;
                }
                @page {
                    margin: 1cm;
                }
                .print-header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #000;
                    padding-bottom: 10px;
                }
                .print-footer {
                    margin-top: 20px;
                    text-align: center;
                    font-size: 10px;
                    border-top: 1px solid #ccc;
                    padding-top: 10px;
                }
            </style>
        `;
        
        // Add company header for print
        const printHeader = `
            <div class="print-header">
                <h1>Malaysian Grocery Store</h1>
                <p>123 Jalan Sample, Taman Test, 50000 Kuala Lumpur, Malaysia</p>
                <p>Tel: +60 12-345 6789 | Email: info@grocerystore.my</p>
            </div>
        `;
        
        // Add print footer
        const printFooter = `
            <div class="print-footer">
                <p>Thank you for your business! | Printed on: ${new Date().toLocaleString()}</p>
                <p>This is a computer-generated receipt.</p>
            </div>
        `;
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Order #<?= $order['id'] ?> - Receipt</title>
                ${printStyles}
            </head>
            <body>
                ${printHeader}
                ${orderContent.outerHTML}
                ${printFooter}
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.focus();
        
        // Wait for content to load then print
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    }
    
    // Update the print button onclick
    const printBtn = document.querySelector('.print-btn');
    if (printBtn) {
        printBtn.onclick = printOrder;
    }
</script> 