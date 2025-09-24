<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config1/mongodb.php';
require_once '../models/User.php';

$userModel = new User();

// Handle user operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    try {
        switch ($_POST['action']) {
            case 'create_user':
                $userData = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                    'contact_number' => $_POST['contact_number'] ?? '',
                    'gender' => $_POST['gender'] ?? '',
                    'region' => $_POST['region'] ?? '',
                    'city' => $_POST['city'] ?? '',
                    'full_name' => $_POST['full_name'] ?? ''
                ];
                $userModel->createUser($userData);
                $response = ['success' => true, 'message' => 'User created successfully'];
                break;
                
            case 'update_status':
                $userId = $_POST['user_id'];
                $newStatus = $_POST['status'];
                $userModel->updateStatus($userId, $newStatus);
                $response = ['success' => true, 'message' => 'User status updated successfully'];
                break;
                
            case 'delete_user':
                $userId = $_POST['user_id'];
                $userModel->deleteUser($userId);
                $response = ['success' => true, 'message' => 'User deleted successfully'];
                break;
                
            case 'get_user_details':
                $userId = $_POST['user_id'];
                $user = $userModel->getById($userId);
                $response = ['success' => true, 'data' => $user];
                break;
                
            case 'update_user':
                $userId = $_POST['user_id'];
                $updateData = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'contact_number' => $_POST['contact_number'] ?? '',
                    'gender' => $_POST['gender'] ?? '',
                    'region' => $_POST['region'] ?? '',
                    'city' => $_POST['city'] ?? '',
                    'full_name' => $_POST['full_name'] ?? ''
                ];
                
                // Only update password if provided
                if (!empty($_POST['password'])) {
                    $updateData['password'] = $_POST['password'];
                }
                
                $userModel->updateUser($userId, $updateData);
                $response = ['success' => true, 'message' => 'User updated successfully'];
                break;
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => $e->getMessage()];
    }
    
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Get user statistics
$userStats = $userModel->getUserStatistics();
$users = $userModel->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
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
            min-height: 100vh;
            color: #3E2723;
            display: flex;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
            overflow-y: auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            backdrop-filter: blur(10px);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #3E2723, #5D4037);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-top: 5px;
        }

        .add-user-btn {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .add-user-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(76, 175, 80, 0.3);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(62, 39, 35, 0.15);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.total { background: linear-gradient(135deg, #4CAF50, #2E7D32); }
        .stat-icon.active { background: linear-gradient(135deg, #2196F3, #1976D2); }
        .stat-icon.inactive { background: linear-gradient(135deg, #FF9800, #F57C00); }
        .stat-icon.online { background: linear-gradient(135deg, #9C27B0, #7B1FA2); }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #3E2723;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Users Table */
        .users-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .table-header {
            padding: 25px 30px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #3E2723;
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 20px;
            border: 2px solid rgba(62, 39, 35, 0.1);
            border-radius: 25px;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #29B6F6;
            box-shadow: 0 0 0 3px rgba(41, 182, 246, 0.1);
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th {
            background: rgba(62, 39, 35, 0.05);
            padding: 20px 15px;
            text-align: left;
            font-weight: 600;
            color: #3E2723;
            border-bottom: 2px solid rgba(62, 39, 35, 0.1);
        }

        .users-table td {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.05);
            vertical-align: middle;
        }

        .users-table tr:hover {
            background: rgba(41, 182, 246, 0.05);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: #3E2723;
            margin-bottom: 2px;
        }

        .user-email {
            font-size: 0.85rem;
            color: #666;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: rgba(76, 175, 80, 0.1);
            color: #2E7D32;
        }

        .status-inactive {
            background: rgba(244, 67, 54, 0.1);
            color: #C62828;
        }

        .status-online {
            background: rgba(156, 39, 176, 0.1);
            color: #7B1FA2;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
        }

        .btn-view {
            background: rgba(41, 182, 246, 0.1);
            color: #1976D2;
        }

        .btn-view:hover {
            background: rgba(41, 182, 246, 0.2);
        }

        .btn-edit {
            background: rgba(255, 152, 0, 0.1);
            color: #F57C00;
        }

        .btn-edit:hover {
            background: rgba(255, 152, 0, 0.2);
        }

        .btn-delete {
            background: rgba(244, 67, 54, 0.1);
            color: #C62828;
        }

        .btn-delete:hover {
            background: rgba(244, 67, 54, 0.2);
        }

        .btn-toggle {
            background: rgba(76, 175, 80, 0.1);
            color: #2E7D32;
        }

        .btn-toggle:hover {
            background: rgba(76, 175, 80, 0.2);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 25px 30px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #3E2723;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #666;
            cursor: pointer;
            padding: 5px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: rgba(244, 67, 54, 0.1);
            color: #C62828;
        }

        .modal-body {
            padding: 30px;
        }

        .user-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .detail-item {
            background: rgba(62, 39, 35, 0.05);
            padding: 20px;
            border-radius: 12px;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: #3E2723;
        }

        .activity-log {
            background: rgba(62, 39, 35, 0.05);
            padding: 20px;
            border-radius: 12px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #3E2723;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(62, 39, 35, 0.1);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(62, 39, 35, 0.1);
            border-radius: 10px;
            font-size: 1rem;
            background: white;
            cursor: pointer;
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid rgba(62, 39, 35, 0.1);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-cancel {
            background: rgba(62, 39, 35, 0.1);
            color: #3E2723;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-save {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #8D6E63;
            cursor: pointer;
            padding: 5px;
        }
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
        }

        .activity-info {
            flex: 1;
        }

        .activity-action {
            font-weight: 600;
            color: #3E2723;
            margin-bottom: 2px;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .search-box {
                width: 100%;
                margin-top: 15px;
            }
            
            .table-header {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/admin-sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">User Management</h1>
                <p class="page-subtitle">Manage registered users and their activities</p>
            </div>
            <button class="add-user-btn" onclick="showCreateUserModal()">
                <i class="fas fa-plus"></i>
                Add User
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon total">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-number"><?php echo $userStats['total_users']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon active">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
                <div class="stat-number"><?php echo $userStats['active_users']; ?></div>
                <div class="stat-label">Active Users</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon inactive">
                        <i class="fas fa-user-times"></i>
                    </div>
                </div>
                <div class="stat-number"><?php echo $userStats['inactive_users']; ?></div>
                <div class="stat-label">Inactive Users</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon online">
                        <i class="fas fa-circle"></i>
                    </div>
                </div>
                <div class="stat-number"><?php echo $userStats['online_users']; ?></div>
                <div class="stat-label">Online Today</div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="users-container">
            <div class="table-header">
                <h2 class="table-title">Registered Users</h2>
                <div class="search-box">
                    <input type="text" id="userSearch" placeholder="Search users...">
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <?php foreach ($users as $user): ?>
                        <tr data-user-id="<?php echo $user['_id']; ?>">
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                    </div>
                                    <div class="user-info">
                                        <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                                        <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name"><?php echo htmlspecialchars($user['contact_number']); ?></div>
                                    <div class="user-email"><?php echo ucfirst($user['gender']); ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name"><?php echo ucfirst(str_replace('-', ' ', $user['region'])); ?></div>
                                    <div class="user-email"><?php echo ucfirst(str_replace('-', ' ', $user['city'])); ?></div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $user['status']; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></div>
                                    <div class="user-email"><?php echo date('g:i A', strtotime($user['created_at'])); ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">
                                        <?php 
                                        if (isset($user['last_login'])) {
                                            echo date('M j, Y', strtotime($user['last_login']));
                                        } else {
                                            echo 'Never';
                                        }
                                        ?>
                                    </div>
                                    <div class="user-email">
                                        <?php 
                                        if (isset($user['last_login'])) {
                                            echo date('g:i A', strtotime($user['last_login']));
                                        } else {
                                            echo '';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-view" onclick="viewUser('<?php echo $user['_id']; ?>')" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-edit" onclick="editUser('<?php echo $user['_id']; ?>')" title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-toggle" onclick="toggleUserStatus('<?php echo $user['_id']; ?>', '<?php echo $user['status']; ?>')" title="<?php echo $user['status'] === 'active' ? 'Deactivate' : 'Activate'; ?> User">
                                        <i class="fas fa-<?php echo $user['status'] === 'active' ? 'ban' : 'check'; ?>"></i>
                                    </button>
                                    <button class="btn btn-delete" onclick="deleteUser('<?php echo $user['_id']; ?>')" title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">User Details</h3>
                <button class="close-btn" onclick="closeModal('userModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="userModalBody">
                <!-- User details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    <div id="createUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Create New User</h3>
                <button class="modal-close" onclick="closeModal('createUserModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="createUserForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Region</label>
                        <input type="text" name="region" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('createUserModal')">Cancel</button>
                    <button type="submit" class="btn-save">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit User</h3>
                <button class="modal-close" onclick="closeModal('editUserModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editUserForm">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" id="editUsername" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" id="editEmail" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" id="editFullName" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" id="editContactNumber" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" id="editGender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Region</label>
                        <input type="text" name="region" id="editRegion" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" id="editCity" class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('editUserModal')">Cancel</button>
                    <button type="submit" class="btn-save">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <script src="includes/admin-sidebar.js"></script>
    <script>
        // Search functionality
        document.getElementById('userSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTableBody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // View user details
        function viewUser(userId) {
            fetch('manage-users.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_user_details&user_id=${userId}&ajax=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.data;
                    const modalBody = document.getElementById('userModalBody');
                    
                    modalBody.innerHTML = `
                        <div class="user-detail-grid">
                            <div class="detail-item">
                                <div class="detail-label">Username</div>
                                <div class="detail-value">${user.username || 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Email</div>
                                <div class="detail-value">${user.email || 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Contact Number</div>
                                <div class="detail-value">${user.contact_number || 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Gender</div>
                                <div class="detail-value">${user.gender ? user.gender.charAt(0).toUpperCase() + user.gender.slice(1) : 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Region</div>
                                <div class="detail-value">${user.region ? user.region.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">City</div>
                                <div class="detail-value">${user.city ? user.city.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Status</div>
                                <div class="detail-value">
                                    <span class="status-badge status-${user.status || 'active'}">${(user.status || 'active').charAt(0).toUpperCase() + (user.status || 'active').slice(1)}</span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Role</div>
                                <div class="detail-value">${user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : 'User'}</div>
                            </div>
                        </div>
                        
                        <div class="activity-log">
                            <h4 style="margin-bottom: 20px; color: #3E2723;">Account Activity</h4>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="activity-info">
                                    <div class="activity-action">Account Created</div>
                                    <div class="activity-time">${user.created_at ? new Date(user.created_at).toLocaleString() : 'N/A'}</div>
                                </div>
                            </div>
                            ${user.last_login ? `
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <div class="activity-info">
                                    <div class="activity-action">Last Login</div>
                                    <div class="activity-time">${new Date(user.last_login).toLocaleString()}</div>
                                </div>
                            </div>
                            ` : ''}
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="activity-info">
                                    <div class="activity-action">Profile Updated</div>
                                    <div class="activity-time">${user.updated_at ? new Date(user.updated_at).toLocaleString() : 'N/A'}</div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('userModal').classList.add('active');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading user details');
            });
        }

        // Toggle user status
        function toggleUserStatus(userId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            const action = currentStatus === 'active' ? 'deactivate' : 'activate';
            
            if (confirm(`Are you sure you want to ${action} this user?`)) {
                fetch('manage-users.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_status&user_id=${userId}&status=${newStatus}&ajax=1`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        // Delete user
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch('manage-users.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_user&user_id=${userId}&ajax=1`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('active');
            }
        });

        // Show create user modal
        function showCreateUserModal() {
            document.getElementById('createUserModal').classList.add('active');
        }

        // Edit user
        function editUser(userId) {
            fetch('manage-users.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_user_details&user_id=' + userId + '&ajax=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.data;
                    document.getElementById('editUserId').value = user._id;
                    document.getElementById('editUsername').value = user.username || '';
                    document.getElementById('editEmail').value = user.email || '';
                    document.getElementById('editContactNumber').value = user.contact_number || '';
                    document.getElementById('editGender').value = user.gender || '';
                    document.getElementById('editRegion').value = user.region || '';
                    document.getElementById('editCity').value = user.city || '';
                    document.getElementById('editFullName').value = user.full_name || '';
                    document.getElementById('editUserModal').classList.add('active');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading user details');
            });
        }

        // Form submissions
        document.getElementById('createUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'create_user');
            formData.append('ajax', '1');

            fetch('manage-users.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User created successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });

        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_user');
            formData.append('ajax', '1');

            fetch('manage-users.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });
    </script>

    <!-- Username Validation CSS -->
    <link rel="stylesheet" href="../styles/username-validation.css">

    <!-- Username Validation JavaScript -->
    <script src="../scripts/username-validation.js"></script>
</body>
</html>

