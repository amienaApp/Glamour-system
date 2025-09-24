<?php
/**
 * Migration script to update existing feedback records
 * from old status field structure to new is_read/is_replied structure
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config1/mongodb.php';

$db = MongoDB::getInstance();
$feedbackCollection = $db->getCollection('feedback');

$message = '';
$messageType = '';

// Handle migration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'migrate') {
    try {
        // Find all feedback records that still use the old status field
        $oldRecords = $feedbackCollection->find(['status' => ['$exists' => true]])->toArray();
        
        $migratedCount = 0;
        
        foreach ($oldRecords as $record) {
            $updateData = [];
            
            // Convert old status to new structure
            if (isset($record['status'])) {
                switch ($record['status']) {
                    case 'unread':
                        $updateData['is_read'] = false;
                        $updateData['is_replied'] = false;
                        break;
                    case 'read':
                        $updateData['is_read'] = true;
                        $updateData['is_replied'] = false;
                        break;
                    case 'replied':
                        $updateData['is_read'] = true; // Assume replied items were also read
                        $updateData['is_replied'] = true;
                        break;
                }
                
                // Remove old status field
                $updateData['$unset'] = ['status' => 1];
                
                // Update the record
                $feedbackCollection->updateOne(
                    ['_id' => $record['_id']],
                    $updateData
                );
                
                $migratedCount++;
            }
        }
        
        $message = "Successfully migrated $migratedCount feedback records to the new structure.";
        $messageType = 'success';
        
    } catch (Exception $e) {
        $message = "Migration failed: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Check how many records need migration
$oldRecordsCount = $feedbackCollection->countDocuments(['status' => ['$exists' => true]]);
$newRecordsCount = $feedbackCollection->countDocuments(['is_read' => ['$exists' => true]]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migrate Feedback - Admin Dashboard</title>
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

        .migration-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 30px;
        }

        .migration-card h2 {
            color: #3E2723;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .migration-card h2 i {
            color: #FF6B9D;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 107, 157, 0.1);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            border: 2px solid rgba(255, 107, 157, 0.2);
        }

        .stat-card h3 {
            color: #3E2723;
            margin-bottom: 10px;
            font-size: 1rem;
            font-weight: 600;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: 700;
            color: #FF6B9D;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FF6B9D 0%, #FF8E9B 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 157, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
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

        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border: 2px solid rgba(231, 76, 60, 0.2);
        }

        .alert-info {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
            border: 2px solid rgba(52, 152, 219, 0.2);
        }

        .info-box {
            background: rgba(52, 152, 219, 0.05);
            border: 2px solid rgba(52, 152, 219, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-box h3 {
            color: #3498db;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-box p {
            color: #666;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
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
            <h1><i class="fas fa-database"></i> Migrate Feedback Data</h1>
            <p>Update existing feedback records to use the new read/replied structure</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : ($messageType === 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="migration-card">
            <h2><i class="fas fa-info-circle"></i> Migration Status</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Old Structure Records</h3>
                    <div class="number"><?php echo $oldRecordsCount; ?></div>
                </div>
                <div class="stat-card">
                    <h3>New Structure Records</h3>
                    <div class="number"><?php echo $newRecordsCount; ?></div>
                </div>
            </div>

            <div class="info-box">
                <h3><i class="fas fa-lightbulb"></i> What This Migration Does</h3>
                <p>
                    This migration updates existing feedback records from the old single "status" field 
                    to the new "is_read" and "is_replied" boolean fields. This allows feedback to be 
                    both read and replied to independently, fixing the issue where marking feedback 
                    as "replied" would reset the "read" count.
                </p>
            </div>

            <?php if ($oldRecordsCount > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Migration Required:</strong> You have <?php echo $oldRecordsCount; ?> feedback record(s) 
                    that need to be migrated to the new structure.
                </div>

                <form method="POST" onsubmit="return confirm('Are you sure you want to migrate the feedback data? This action cannot be undone.')">
                    <input type="hidden" name="action" value="migrate">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-database"></i> Migrate Feedback Data
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>All Up to Date:</strong> All feedback records are already using the new structure. No migration needed.
                </div>
            <?php endif; ?>

            <div style="margin-top: 30px;">
                <a href="manage-feedback.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Feedback Management
                </a>
            </div>
        </div>
    </div>
    
    <script src="includes/admin-sidebar.js"></script>
</body>
</html>

