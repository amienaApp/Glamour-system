<?php
/**
 * Admin Payment Management
 * View and manage all payments in the system
 */

require_once '../models/Payment.php';
require_once '../models/User.php';
require_once '../models/Order.php';

/**
 * Helper function to safely format order ID for display
 */
function formatOrderId($orderId) {
    if (is_object($orderId)) {
        if (method_exists($orderId, 'toArray')) {
            $array = $orderId->toArray();
            if (isset($array['$oid'])) {
                return $array['$oid'];
            }
        }
        // Don't try to convert to string, just return a safe fallback
        return 'Unknown Order';
    }
    return $orderId;
}

$paymentModel = new Payment();
$userModel = new User();
$orderModel = new Order();

// Get payment statistics
$stats = $paymentModel->getPaymentStatistics();

// Get all payments with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$skip = ($page - 1) * $limit;

$payments = $paymentModel->getAllPayments([
    'sort' => ['created_at' => -1],
    'skip' => $skip,
    'limit' => $limit
]);

$totalPayments = $paymentModel->countPayments();
$totalPages = ceil($totalPayments / $limit);

// Handle bulk delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete') {
    $paymentIds = $_POST['payment_ids'] ?? [];
    $successCount = 0;
    $errorCount = 0;
    
    if (!empty($paymentIds)) {
        foreach ($paymentIds as $paymentId) {
            if ($paymentModel->delete($paymentId)) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }
        
        if ($successCount > 0) {
            $message = "Successfully deleted $successCount payment" . ($successCount !== 1 ? 's' : '') . "!";
            if ($errorCount > 0) {
                $message .= " Failed to delete $errorCount payment" . ($errorCount !== 1 ? 's' : '') . ".";
            }
            $messageType = 'success';
        } else {
            $message = 'Failed to delete any payments.';
            $messageType = 'error';
        }
    } else {
        $message = 'No payments selected for deletion.';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="includes/admin-sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Circular Std', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #E3F2FD 0%, #B3E5FC 50%, #81D4FA 100%);
            min-height: 100vh;
            color: #3E2723;
            display: flex;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        .header {
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(62,39,35,0.1);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .header h1 {
            color: #3E2723;
            font-size: 2.8rem;
            margin-bottom: 15px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            border-left: 5px solid #29B6F6;
            box-shadow: 0 8px 25px rgba(62,39,35,0.05);
        }

        .stat-card h3 {
            color: #3E2723;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .stat-card p {
            color: #3E2723;
            font-size: 1rem;
            font-weight: 600;
            opacity: 0.8;
        }

        .stat-card.completed { border-left: 4px solid #28a745; }
        .stat-card.pending { border-left: 4px solid #ffc107; }
        .stat-card.failed { border-left: 4px solid #dc3545; }
        .stat-card.total { border-left: 4px solid #007bff; }

        .payments-table {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(62, 39, 35, 0.08);
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .table-header {
            background: linear-gradient(135deg, #29B6F6 0%, #0288D1 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-completed { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-failed { background: #f8d7da; color: #721c24; }

        .method-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
        }

        .method-waafi { background: #0066cc; }
        .method-card { background: #6f42c1; }
        .method-bank { background: #17a2b8; }

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
            margin-right: 5px;
        }

        .btn-view { background: #007bff; color: white; }
        .btn-edit { background: #28a745; color: white; }
        .btn-delete { background: #dc3545; color: white; }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }

        .pagination a:hover {
            background: #007bff;
            color: white;
        }

        .pagination .current {
            background: #007bff;
            color: white;
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Bulk Actions Styles */
        .bulk-actions {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 25px rgba(62, 39, 35, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .bulk-actions-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .bulk-actions-buttons {
            display: flex;
            gap: 10px;
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h2 {
            margin: 0;
            color: #0066cc;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .modal-close:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 2px rgba(0,102,204,0.1);
        }

        .modal-actions {
            text-align: right;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .btn-cancel {
            padding: 10px 20px;
            margin-right: 10px;
            background: #ccc;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-submit {
            padding: 10px 20px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-cancel:hover {
            background: #bbb;
        }

        .btn-submit:hover {
            background: #0052a3;
        }

        .btn-delete, .btn-clear {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-delete {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
        }

        .btn-clear {
            background: rgba(62, 39, 35, 0.1);
            color: #3E2723;
        }

        .btn-clear:hover {
            background: rgba(62, 39, 35, 0.2);
            transform: translateY(-2px);
        }

        /* Checkbox Styles */
        #selectAll, .payment-select {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #29B6F6;
        }

        /* Message Styles */
        .message {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-weight: 500;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php include 'includes/admin-sidebar.php'; ?>
    
    <div class="main-content">
            <div class="header">
                <h1>Payment Management</h1>
                <p>View and manage all payment transactions</p>
            </div>

            <?php if (isset($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card total">
                    <h3><?php echo $stats['total_payments']; ?></h3>
                    <p>Total Payments</p>
                </div>
                <div class="stat-card completed">
                    <h3><?php echo $stats['completed_payments']; ?></h3>
                    <p>Completed</p>
                </div>
                <div class="stat-card pending">
                    <h3><?php echo $stats['pending_payments']; ?></h3>
                    <p>Pending</p>
                </div>
                <div class="stat-card failed">
                    <h3><?php echo $stats['failed_payments']; ?></h3>
                    <p>Failed</p>
                </div>
            </div>

            <!-- Payment Methods Stats -->
            <div class="stats-grid">
                <?php foreach ($stats['by_method'] as $method => $count): ?>
                    <div class="stat-card">
                        <h3><?php echo $count; ?></h3>
                        <p><?php echo ucfirst($method); ?> Payments</p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Search -->
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search payments..." id="searchInput">
                <button class="search-btn" onclick="searchPayments()">
                    <i class="fas fa-search"></i> Search
                </button>

            </div>

            <!-- Payments Table -->
            <div class="payments-table">
                <div class="table-header">
                    <h2>Recent Payments</h2>
                    <span>Showing <?php echo count($payments); ?> of <?php echo $totalPayments; ?> payments</span>
                </div>
                
                <!-- Bulk Actions Panel -->
                <div id="bulk-actions" class="bulk-actions" style="display: none;">
                    <div class="bulk-actions-content">
                        <span id="selected-count">0 payments selected</span>
                        <div class="bulk-actions-buttons">
                            <button type="button" class="btn-delete" onclick="bulkDeletePayments()">
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                            <button type="button" class="btn-clear" onclick="clearSelection()">
                                <i class="fas fa-times"></i> Clear Selection
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                                <th>Payment ID</th>
                                <th>User</th>
                                <th>Order ID</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><input type="checkbox" class="payment-select" value="<?php echo $payment['_id']; ?>" onchange="updateBulkActions()"></td>
                                    <td><?php echo substr((string)$payment['_id'], -8); ?></td>
                                    <td>
                                        <?php 
                                        // Use stored user information or fallback to database lookup
                                        $userName = 'Unknown User';
                                        
                                        // First, try to use stored user information
                                        if (isset($payment['user_email']) && !empty($payment['user_email'])) {
                                            $userName = $payment['user_email'];
                                        } elseif (isset($payment['user_name']) && !empty($payment['user_name'])) {
                                            $userName = $payment['user_name'];
                                        } else {
                                            // Fallback to database lookup
                                            if ($payment['user_id'] === 'demo_user_123') {
                                                $userName = 'Demo User';
                                            } else {
                                                $user = $userModel->getById($payment['user_id']);
                                                if ($user) {
                                                    if (isset($user['username'])) {
                                                        $userName = $user['username'];
                                                    } elseif (isset($user['email'])) {
                                                        $userName = $user['email'];
                                                    } else {
                                                        $userName = 'User ID: ' . substr((string)$payment['user_id'], -8);
                                                    }
                                                } else {
                                                    $userName = 'User ID: ' . substr((string)$payment['user_id'], -8);
                                                }
                                            }
                                        }
                                        
                                        echo htmlspecialchars($userName);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        try {
                                        $order = $orderModel->getOrderById($payment['order_id']);
                                            if ($order && isset($order['order_number'])) {
                                                echo $order['order_number'];
                                            } else {
                                                echo formatOrderId($payment['order_id']);
                                            }
                                        } catch (Exception $e) {
                                            echo formatOrderId($payment['order_id']);
                                        }
                                        ?>
                                    </td>
                                    <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td>
                                        <span class="method-badge method-<?php echo $payment['payment_method']; ?>">
                                            <?php echo strtoupper($payment['payment_method']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $payment['status']; ?>">
                                            <?php echo ucfirst($payment['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y H:i', strtotime($payment['created_at'])); ?></td>
                                    <td>
                                        <button class="action-btn btn-view" onclick="viewPayment('<?php echo (string)$payment['_id']; ?>')" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn btn-edit" onclick="editPayment('<?php echo (string)$payment['_id']; ?>')" type="button">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn btn-delete" onclick="deletePayment('<?php echo (string)$payment['_id']; ?>')" type="button">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    <script src="includes/admin-sidebar.js"></script>
    <script>

        
        // Add event listeners after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            
        });
        function searchPayments() {
            const searchTerm = document.getElementById('searchInput').value;
            if (searchTerm.trim()) {
                window.location.href = `?search=${encodeURIComponent(searchTerm)}`;
            }
        }

        function viewPayment(paymentId) {
    
            
            // Fetch payment details and show modal
            fetch('payment-actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'view',
                    payment_id: paymentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPaymentModal('View Payment', data.payment, 'view');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function editPayment(paymentId) {
    
            
            // Fetch payment details and show edit modal
            fetch('payment-actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get',
                    payment_id: paymentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPaymentModal('Edit Payment', data.payment, 'edit');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function deletePayment(paymentId) {
    
            showDeleteModal(paymentId);
        }

        function showDeleteModal(paymentId) {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            `;

            const modalContent = document.createElement('div');
            modalContent.style.cssText = `
                background: white;
                padding: 30px;
                border-radius: 15px;
                max-width: 400px;
                width: 90%;
                text-align: center;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            `;

            modalContent.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
                    <h2 style="margin: 0; color: #333;">Delete Payment</h2>
                </div>
                <p style="margin-bottom: 25px; color: #666; line-height: 1.5;">
                    Are you sure you want to delete this payment?<br>
                    <strong>This action cannot be undone.</strong>
                </p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button onclick="this.closest('.modal').remove()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                    <button onclick="confirmDelete('${paymentId}', this)" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">Delete Payment</button>
                </div>
            `;

            modal.appendChild(modalContent);
            document.body.appendChild(modal);
        }

        function showBulkDeleteModal(paymentIds) {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            `;

            const modalContent = document.createElement('div');
            modalContent.style.cssText = `
                background: white;
                padding: 30px;
                border-radius: 15px;
                max-width: 400px;
                width: 90%;
                text-align: center;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            `;

            modalContent.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
                    <h2 style="margin: 0; color: #333;">Delete Multiple Payments</h2>
                </div>
                <p style="margin-bottom: 25px; color: #666; line-height: 1.5;">
                    Are you sure you want to delete <strong>${paymentIds.length} payment(s)</strong>?<br>
                    <strong>This action cannot be undone.</strong>
                </p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button onclick="this.closest('.modal').remove()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                    <button onclick="confirmBulkDelete(${JSON.stringify(paymentIds)}, this)" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">Delete ${paymentIds.length} Payment(s)</button>
                </div>
            `;

            modal.appendChild(modalContent);
            document.body.appendChild(modal);
        }

        function confirmBulkDelete(paymentIds, button) {
            // Disable button and show loading
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            
            fetch('payment-actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'bulk_delete',
                    payment_ids: paymentIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const modal = button.closest('.modal');
                    const modalContent = modal.querySelector('div');
                    modalContent.innerHTML = `
                        <div style="margin-bottom: 20px;">
                            <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745; margin-bottom: 15px;"></i>
                            <h2 style="margin: 0; color: #333;">Success!</h2>
                        </div>
                        <p style="margin-bottom: 25px; color: #666;">
                            Successfully deleted ${data.deleted_count} payment(s)!
                        </p>
                        <button onclick="location.reload()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">OK</button>
                    `;
                } else {
                    // Show error message
                    const modal = button.closest('.modal');
                    const modalContent = modal.querySelector('div');
                    modalContent.innerHTML = `
                        <div style="margin-bottom: 20px;">
                            <i class="fas fa-times-circle" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
                            <h2 style="margin: 0; color: #333;">Error</h2>
                        </div>
                        <p style="margin-bottom: 25px; color: #666;">
                            ${data.message}
                        </p>
                        <button onclick="this.closest('.modal').remove()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>
                    `;
                }
            })
            .catch(error => {
                // Show error message
                const modal = button.closest('.modal');
                const modalContent = modal.querySelector('div');
                modalContent.innerHTML = `
                    <div style="margin-bottom: 20px;">
                        <i class="fas fa-times-circle" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
                        <h2 style="margin: 0; color: #333;">Error</h2>
                    </div>
                    <p style="margin-bottom: 25px; color: #666;">
                        ${error.message}
                    </p>
                    <button onclick="this.closest('.modal').remove()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>
                `;
            });
        }

        function confirmDelete(paymentId, button) {
            // Disable button and show loading
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            
            fetch('payment-actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete',
                    payment_id: paymentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const modal = button.closest('.modal');
                    const modalContent = modal.querySelector('div');
                    modalContent.innerHTML = `
                        <div style="margin-bottom: 20px;">
                            <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745; margin-bottom: 15px;"></i>
                            <h2 style="margin: 0; color: #333;">Success!</h2>
                        </div>
                        <p style="margin-bottom: 25px; color: #666;">
                            Payment deleted successfully!
                        </p>
                        <button onclick="location.reload()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">OK</button>
                    `;
                } else {
                    // Show error message
                    const modal = button.closest('.modal');
                    const modalContent = modal.querySelector('div');
                    modalContent.innerHTML = `
                        <div style="margin-bottom: 20px;">
                            <i class="fas fa-times-circle" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
                            <h2 style="margin: 0; color: #333;">Error</h2>
                        </div>
                        <p style="margin-bottom: 25px; color: #666;">
                            ${data.message}
                        </p>
                        <button onclick="this.closest('.modal').remove()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>
                    `;
                }
            })
            .catch(error => {
                // Show error message
                const modal = button.closest('.modal');
                const modalContent = modal.querySelector('div');
                modalContent.innerHTML = `
                    <div style="margin-bottom: 20px;">
                        <i class="fas fa-times-circle" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
                        <h2 style="margin: 0; color: #333;">Error</h2>
                    </div>
                    <p style="margin-bottom: 25px; color: #666;">
                        ${error.message}
                    </p>
                    <button onclick="this.closest('.modal').remove()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>
                `;
            });
        }

        function showPaymentModal(title, payment, mode) {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            `;

            const modalContent = document.createElement('div');
            modalContent.style.cssText = `
                background: white;
                padding: 30px;
                border-radius: 15px;
                max-width: 600px;
                width: 90%;
                max-height: 80vh;
                overflow-y: auto;
            `;

            let content = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; color: #0066cc;">${title}</h2>
                    <button onclick="this.closest('.modal').remove()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
            `;

            if (mode === 'view') {
                content += `
                    <div style="margin-bottom: 15px;">
                        <strong>Payment ID:</strong> ${payment._id}<br>
                        <strong>Order ID:</strong> ${payment.order_id}<br>
                        <strong>User ID:</strong> ${payment.user_id}<br>
                        <strong>Amount:</strong> $${parseFloat(payment.amount).toFixed(2)}<br>
                        <strong>Payment Method:</strong> ${payment.payment_method}<br>
                        <strong>Status:</strong> <span style="color: ${payment.status === 'completed' ? 'green' : payment.status === 'pending' ? 'orange' : 'red'};">${payment.status}</span><br>
                        <strong>Created:</strong> ${payment.created_at}<br>
                        <strong>Updated:</strong> ${payment.updated_at}
                    </div>
                `;

                if (payment.payment_details) {
                    content += '<div style="margin-top: 20px;"><strong>Payment Details:</strong><br>';
                    for (const [key, value] of Object.entries(payment.payment_details)) {
                        content += `${key}: ${value}<br>`;
                    }
                    content += '</div>';
                }
            } else if (mode === 'edit') {
                content += `
                    <form id="editPaymentForm">
                        <input type="hidden" name="payment_id" value="${payment._id}">
                        <div style="margin-bottom: 15px;">
                            <label><strong>Status:</strong></label><br>
                            <select name="status" style="width: 100%; padding: 8px; margin-top: 5px;">
                                <option value="pending" ${payment.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="completed" ${payment.status === 'completed' ? 'selected' : ''}>Completed</option>
                                <option value="failed" ${payment.status === 'failed' ? 'selected' : ''}>Failed</option>
                                <option value="cancelled" ${payment.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label><strong>Payment Method:</strong></label><br>
                            <select name="payment_method" style="width: 100%; padding: 8px; margin-top: 5px;">
                                <option value="waafi" ${payment.payment_method === 'waafi' ? 'selected' : ''}>Waafi</option>
                                <option value="card" ${payment.payment_method === 'card' ? 'selected' : ''}>Card</option>
                                <option value="bank" ${payment.payment_method === 'bank' ? 'selected' : ''}>Bank</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label><strong>Amount:</strong></label><br>
                            <input type="number" name="amount" value="${payment.amount}" step="0.01" style="width: 100%; padding: 8px; margin-top: 5px;">
                        </div>
                        <div style="text-align: right;">
                            <button type="button" onclick="this.closest('.modal').remove()" style="padding: 10px 20px; margin-right: 10px; background: #ccc; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                            <button type="submit" style="padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer;">Update Payment</button>
                        </div>
                    </form>
                `;
            }

            modalContent.innerHTML = content;
            modal.appendChild(modalContent);
            document.body.appendChild(modal);

            // Handle form submission for edit mode
            if (mode === 'edit') {
                const form = modal.querySelector('#editPaymentForm');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(form);
                    const data = Object.fromEntries(formData.entries());
                    data.action = 'update';

                    fetch('payment-actions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Payment updated successfully!');
                            modal.remove();
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Error: ' + error.message);
                    });
                });
            }
        }

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchPayments();
            }
        });

        // Bulk Actions Functions
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const paymentCheckboxes = document.querySelectorAll('.payment-select');
            
            paymentCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            
            updateBulkActions();
        }

        function updateBulkActions() {
            const selectedCheckboxes = document.querySelectorAll('.payment-select:checked');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            const selectAllCheckbox = document.getElementById('selectAll');
            
            const selectedCountValue = selectedCheckboxes.length;
            selectedCount.textContent = `${selectedCountValue} payment${selectedCountValue !== 1 ? 's' : ''} selected`;
            
            if (selectedCountValue > 0) {
                bulkActions.style.display = 'block';
            } else {
                bulkActions.style.display = 'none';
            }
            
            // Update select all checkbox state
            const totalCheckboxes = document.querySelectorAll('.payment-select');
            selectAllCheckbox.checked = selectedCountValue === totalCheckboxes.length && totalCheckboxes.length > 0;
        }

        function bulkDeletePayments() {
            const selectedCheckboxes = document.querySelectorAll('.payment-select:checked');
            const paymentIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
            
            if (paymentIds.length === 0) {
                alert('Please select payments to delete');
                return;
            }
            
            showBulkDeleteModal(paymentIds);
        }

        function clearSelection() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const paymentCheckboxes = document.querySelectorAll('.payment-select');
            
            selectAllCheckbox.checked = false;
            paymentCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            updateBulkActions();
        }
    </script>
</body>
</html>
