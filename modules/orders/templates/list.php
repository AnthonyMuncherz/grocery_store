<?php
/**
 * Orders List Template
 */
?>

<style>
    .orders-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .orders-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e1e8ed;
    }

    .orders-header h2 {
        color: #2c3e50;
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .add-order-btn {
        background: #27ae60;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .add-order-btn:hover {
        background: #219a52;
        text-decoration: none;
        color: white;
    }

    .orders-filters {
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-group label {
        font-weight: 600;
        color: #34495e;
        font-size: 0.9rem;
    }

    .filter-group select,
    .filter-group input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
    }

    .orders-table th,
    .orders-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e1e8ed;
    }

    .orders-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #e1e8ed;
        position: sticky;
        top: 0;
    }

    .orders-table tr:hover {
        background: #f8f9fa;
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
        font-weight: 600;
        color: #27ae60;
    }

    .order-id {
        font-weight: 600;
        color: #3498db;
    }

    .action-links {
        display: flex;
        gap: 10px;
    }

    .action-links a {
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .view-btn {
        background: #3498db;
        color: white;
    }

    .view-btn:hover {
        background: #2980b9;
        color: white;
        text-decoration: none;
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

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 30px;
        gap: 10px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 15px;
        border: 1px solid #ddd;
        color: #3498db;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .pagination a:hover {
        background: #3498db;
        color: white;
        text-decoration: none;
    }

    .pagination .current {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
    }

    .empty-state h3 {
        color: #95a5a6;
        margin-bottom: 15px;
    }

    .empty-state p {
        margin-bottom: 25px;
        font-size: 1.1rem;
    }

    .customer-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .customer-name {
        font-weight: 600;
        color: #2c3e50;
    }

    .customer-email {
        font-size: 0.85rem;
        color: #7f8c8d;
    }

    .alert {
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: 600;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 768px) {
        .orders-table {
            font-size: 0.85rem;
        }
        
        .orders-table th,
        .orders-table td {
            padding: 8px 10px;
        }
        
        .action-links {
            flex-direction: column;
            gap: 5px;
        }
        
        .orders-filters {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="orders-container">
    <div class="orders-header">
        <h2>📋 Orders Management</h2>
        <a href="index.php?module=orders&action=add" class="add-order-btn">+ Create New Order</a>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] === 'success' ? 'success' : 'danger' ?>">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <!-- Filters -->
    <div class="orders-filters">
        <div class="filter-group">
            <label>Status Filter</label>
            <select name="status_filter" id="status_filter">
                <option value="">All Statuses</option>
                <option value="pending" <?= isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="processing" <?= isset($_GET['status']) && $_GET['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                <option value="shipped" <?= isset($_GET['status']) && $_GET['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                <option value="delivered" <?= isset($_GET['status']) && $_GET['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                <option value="cancelled" <?= isset($_GET['status']) && $_GET['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label>Sort By</label>
            <select name="sort_by" id="sort_by">
                <option value="created_at" <?= isset($_GET['sort_by']) && $_GET['sort_by'] == 'created_at' ? 'selected' : '' ?>>Date Created</option>
                <option value="total_amount" <?= isset($_GET['sort_by']) && $_GET['sort_by'] == 'total_amount' ? 'selected' : '' ?>>Total Amount</option>
                <option value="customer_name" <?= isset($_GET['sort_by']) && $_GET['sort_by'] == 'customer_name' ? 'selected' : '' ?>>Customer Name</option>
                <option value="status" <?= isset($_GET['sort_by']) && $_GET['sort_by'] == 'status' ? 'selected' : '' ?>>Status</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label>Order</label>
            <select name="sort_dir" id="sort_dir">
                <option value="DESC" <?= isset($_GET['sort_dir']) && $_GET['sort_dir'] == 'DESC' ? 'selected' : '' ?>>Newest First</option>
                <option value="ASC" <?= isset($_GET['sort_dir']) && $_GET['sort_dir'] == 'ASC' ? 'selected' : '' ?>>Oldest First</option>
            </select>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <h3>No Orders Found</h3>
            <p>There are currently no orders in the system.</p>
            <a href="index.php?module=orders&action=add" class="add-order-btn">Create Your First Order</a>
        </div>
    <?php else: ?>
        <!-- Orders Table -->
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <span class="order-id">#<?= htmlspecialchars($order['id']) ?></span>
                        </td>
                        <td>
                            <div class="customer-info">
                                <div class="customer-name"><?= htmlspecialchars($order['customer_name']) ?></div>
                                <div class="customer-email"><?= htmlspecialchars($order['customer_email']) ?></div>
                            </div>
                        </td>
                        <td>
                            <span class="order-amount">RM <?= number_format($order['total_amount'], 2) ?></span>
                        </td>
                        <td>
                            <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
                                <?= ucfirst(htmlspecialchars($order['status'])) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($order['payment_method']) ?></td>
                        <td><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                        <td>
                            <div class="action-links">
                                <a href="index.php?module=orders&action=view&id=<?= $order['id'] ?>" class="view-btn">View</a>
                                <a href="index.php?module=orders&action=edit&id=<?= $order['id'] ?>" class="edit-btn">Edit</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?module=orders&action=list&page=<?= $current_page - 1 ?>&<?= http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>">← Previous</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                    <?php if ($i == $current_page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?module=orders&action=list&page=<?= $i ?>&<?= http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="?module=orders&action=list&page=<?= $current_page + 1 ?>&<?= http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    // Add filter change handlers
    document.addEventListener('DOMContentLoaded', function() {
        const filters = ['status_filter', 'sort_by', 'sort_dir'];
        
        filters.forEach(function(filterId) {
            const element = document.getElementById(filterId);
            if (element) {
                element.addEventListener('change', function() {
                    applyFilters();
                });
            }
        });
    });
    
    function applyFilters() {
        const params = new URLSearchParams(window.location.search);
        
        // Update URL parameters
        params.set('module', 'orders');
        params.set('action', 'list');
        params.delete('page'); // Reset to first page when filtering
        
        const statusFilter = document.getElementById('status_filter').value;
        const sortBy = document.getElementById('sort_by').value;
        const sortDir = document.getElementById('sort_dir').value;
        
        if (statusFilter) {
            params.set('status', statusFilter);
        } else {
            params.delete('status');
        }
        
        if (sortBy) {
            params.set('sort_by', sortBy);
        } else {
            params.delete('sort_by');
        }
        
        if (sortDir) {
            params.set('sort_dir', sortDir);
        } else {
            params.delete('sort_dir');
        }
        
        // Redirect with new parameters
        window.location.search = params.toString();
    }
</script> 