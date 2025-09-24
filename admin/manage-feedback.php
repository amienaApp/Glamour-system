<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config1/mongodb.php';

$db = MongoDB::getInstance();
$feedbackCollection = $db->getCollection('feedback');

// Handle feedback actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $feedbackId = $_POST['feedback_id'] ?? '';
    
    if ($action === 'mark_read' && $feedbackId) {
        $feedbackCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($feedbackId)],
            ['$set' => ['is_read' => true, 'updated_at' => date('Y-m-d H:i:s')]]
        );
        $message = "Feedback marked as read";
    } elseif ($action === 'mark_replied' && $feedbackId) {
        $feedbackCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($feedbackId)],
            ['$set' => ['is_replied' => true, 'updated_at' => date('Y-m-d H:i:s')]]
        );
        $message = "Feedback marked as replied";
    } elseif ($action === 'delete' && $feedbackId) {
        $feedbackCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($feedbackId)]);
        $message = "Feedback deleted successfully";
    }
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';

// Build query
$query = [];
if ($statusFilter !== 'all') {
    if ($statusFilter === 'unread') {
        $query['is_read'] = ['$ne' => true];
    } elseif ($statusFilter === 'read') {
        $query['is_read'] = true;
    } elseif ($statusFilter === 'replied') {
        $query['is_replied'] = true;
    }
}
if (!empty($searchQuery)) {
    $query['$or'] = [
        ['name' => ['$regex' => $searchQuery, '$options' => 'i']],
        ['email' => ['$regex' => $searchQuery, '$options' => 'i']],
        ['message' => ['$regex' => $searchQuery, '$options' => 'i']]
    ];
}

// Get feedback with pagination
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$skip = ($page - 1) * $limit;

$totalFeedback = $feedbackCollection->countDocuments($query);
$totalPages = ceil($totalFeedback / $limit);

$feedback = $feedbackCollection->find($query, [
    'sort' => ['created_at' => -1],
    'skip' => $skip,
    'limit' => $limit
])->toArray();

// Get statistics
$stats = [
    'total' => $feedbackCollection->countDocuments([]),
    'unread' => $feedbackCollection->countDocuments(['is_read' => ['$ne' => true]]),
    'read' => $feedbackCollection->countDocuments(['is_read' => true]),
    'replied' => $feedbackCollection->countDocuments(['is_replied' => true])
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedback - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="includes/admin-sidebar.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Circular Std', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #E3F2FD 0%, #B3E5FC 50%, #81D4FA 100%);
            color: #333;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }

        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .page-header h1 {
            color: #3E2723;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-header h1 i {
            color: #FF6B9D;
        }

        .page-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(62, 39, 35, 0.15);
        }

        .stat-card h3 {
            color: #3E2723;
            margin-bottom: 10px;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #FF6B9D;
        }

        .filters {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filters select,
        .filters input {
            padding: 12px 15px;
            border: 2px solid rgba(62, 39, 35, 0.1);
            border-radius: 12px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .filters select:focus,
        .filters input:focus {
            outline: none;
            border-color: #FF6B9D;
            box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
        }

        .filters button {
            background: linear-gradient(135deg, #FF6B9D 0%, #FF8E9B 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
        }

        .filters button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 157, 0.4);
        }

        .feedback-table {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, #3E2723 0%, #5D4037 100%);
            color: white;
            padding: 20px 25px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-header i {
            color: #FF6B9D;
        }

        .feedback-item {
            padding: 25px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
            transition: all 0.3s ease;
        }

        .feedback-item:hover {
            background: rgba(255, 107, 157, 0.05);
            transform: translateX(5px);
        }

        .feedback-item:last-child {
            border-bottom: none;
        }

        .feedback-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .feedback-meta {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-unread {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
        }

        .status-read {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(243, 156, 18, 0.3);
        }

        .status-replied {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
        }

        .feedback-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .feedback-content {
            margin-top: 1rem;
        }

        .feedback-message {
            background: rgba(255, 107, 157, 0.05);
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #FF6B9D;
            margin-top: 15px;
            font-style: italic;
            line-height: 1.6;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a,
        .pagination span {
            padding: 12px 16px;
            border: 2px solid rgba(62, 39, 35, 0.1);
            text-decoration: none;
            color: #3E2723;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: linear-gradient(135deg, #FF6B9D 0%, #FF8E9B 100%);
            color: white;
            border-color: #FF6B9D;
            transform: translateY(-2px);
        }

        .pagination .current {
            background: linear-gradient(135deg, #FF6B9D 0%, #FF8E9B 100%);
            color: white;
            border-color: #FF6B9D;
        }

        .alert {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .alert-success {
            background: rgba(39, 174, 96, 0.1);
            color: #27ae60;
            border: 2px solid rgba(39, 174, 96, 0.2);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .filters {
                flex-direction: column;
                align-items: stretch;
            }

            .feedback-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/admin-sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-comments"></i> Manage User Feedback</h1>
            <p>View and manage customer feedback from the contact form</p>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Feedback</h3>
                <div class="number"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Unread</h3>
                <div class="number" style="color: #e74c3c;"><?php echo $stats['unread']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Read</h3>
                <div class="number" style="color: #f39c12;"><?php echo $stats['read']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Replied</h3>
                <div class="number" style="color: #27ae60;"><?php echo $stats['replied']; ?></div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form method="GET" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <select name="status">
                    <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="unread" <?php echo $statusFilter === 'unread' ? 'selected' : ''; ?>>Unread</option>
                    <option value="read" <?php echo $statusFilter === 'read' ? 'selected' : ''; ?>>Read</option>
                    <option value="replied" <?php echo $statusFilter === 'replied' ? 'selected' : ''; ?>>Replied</option>
                </select>
                <input type="text" name="search" placeholder="Search by name, email, or message..." value="<?php echo htmlspecialchars($searchQuery); ?>" style="min-width: 300px;">
                <button type="submit"><i class="fas fa-search"></i> Filter</button>
                <a href="manage-feedback.php" class="btn btn-primary"><i class="fas fa-refresh"></i> Reset</a>
            </form>
        </div>

        <!-- Feedback List -->
        <div class="feedback-table">
            <div class="table-header">
                <i class="fas fa-comments"></i> Customer Feedback (<?php echo $totalFeedback; ?> total)
            </div>

            <?php if (empty($feedback)): ?>
                <div style="padding: 2rem; text-align: center; color: #666;">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>No feedback found matching your criteria.</p>
                </div>
            <?php else: ?>
                <?php foreach ($feedback as $item): ?>
                    <div class="feedback-item">
                        <div class="feedback-header">
                            <div class="feedback-meta">
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <span style="color: #666;"><?php echo htmlspecialchars($item['email']); ?></span>
                                <?php 
                                $statusClass = 'status-unread';
                                $statusText = 'Unread';
                                if (isset($item['is_replied']) && $item['is_replied']) {
                                    $statusClass = 'status-replied';
                                    $statusText = 'Replied';
                                } elseif (isset($item['is_read']) && $item['is_read']) {
                                    $statusClass = 'status-read';
                                    $statusText = 'Read';
                                }
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                                <small style="color: #999;">
                                    <?php echo date('M j, Y g:i A', strtotime($item['created_at'])); ?>
                                </small>
                            </div>
                            <div class="feedback-actions">
                                <?php if (!isset($item['is_read']) || !$item['is_read']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="feedback_id" value="<?php echo $item['_id']; ?>">
                                        <button type="submit" class="btn btn-primary" title="Mark as Read">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if (!isset($item['is_replied']) || !$item['is_replied']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="mark_replied">
                                        <input type="hidden" name="feedback_id" value="<?php echo $item['_id']; ?>">
                                        <button type="submit" class="btn btn-success" title="Mark as Replied">
                                            <i class="fas fa-reply"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this feedback?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="feedback_id" value="<?php echo $item['_id']; ?>">
                                    <button type="submit" class="btn btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="feedback-content">
                            <div class="feedback-message">
                                <?php echo nl2br(htmlspecialchars($item['message'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $statusFilter; ?>&search=<?php echo urlencode($searchQuery); ?>">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>&status=<?php echo $statusFilter; ?>&search=<?php echo urlencode($searchQuery); ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $statusFilter; ?>&search=<?php echo urlencode($searchQuery); ?>">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="includes/admin-sidebar.js"></script>
</body>
</html>
