<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config1/mongodb.php';
require_once '../models/Admin.php';

$adminModel = new Admin();

// Handle admin operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    try {
        switch ($_POST['action']) {
            case 'create_admin':
                $adminData = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                    'role' => $_POST['role'],
                    'full_name' => $_POST['full_name'] ?? '',
                    'phone' => $_POST['phone'] ?? ''
                ];
                $adminModel->createAdmin($adminData);
                $response = ['success' => true, 'message' => 'Admin created successfully'];
                break;
                
            case 'update_status':
                $adminId = $_POST['admin_id'];
                $newStatus = $_POST['status'];
                $adminModel->updateStatus($adminId, $newStatus);
                $response = ['success' => true, 'message' => 'Admin status updated successfully'];
                break;
                
            case 'delete_admin':
                $adminId = $_POST['admin_id'];
                $adminModel->deleteAdmin($adminId);
                $response = ['success' => true, 'message' => 'Admin deleted successfully'];
                break;
                
                         case 'get_admin_details':
                 $adminId = $_POST['admin_id'];
                 $admin = $adminModel->getById($adminId);
                 if ($admin) {
                     // Ensure consistent field names for frontend
                     if (isset($admin['name']) && !isset($admin['username'])) {
                         $admin['username'] = $admin['name'];
                     }
                     if (!isset($admin['status'])) {
                         $admin['status'] = 'active';
                     }
                     if (!isset($admin['role'])) {
                         $admin['role'] = 'admin';
                     }
                 }
                 $response = ['success' => true, 'data' => $admin];
                 break;
                
                         case 'update_admin':
                 $adminId = $_POST['admin_id'];
                 $updateData = [
                     'username' => $_POST['username'],
                     'email' => $_POST['email'],
                     'role' => $_POST['role'],
                     'full_name' => $_POST['full_name'] ?? '',
                     'phone' => $_POST['phone'] ?? ''
                 ];
                 
                 // Only update password if provided
                 if (!empty($_POST['password'])) {
                     $updateData['password'] = $_POST['password'];
                 }
                 
                 // Handle existing admin with 'name' field
                 $existingAdmin = $adminModel->getById($adminId);
                 if ($existingAdmin && isset($existingAdmin['name']) && !isset($existingAdmin['username'])) {
                     // Update the name field instead of username
                     $updateData['name'] = $updateData['username'];
                     unset($updateData['username']);
                 }
                 
                 $adminModel->updateAdmin($adminId, $updateData);
                 $response = ['success' => true, 'message' => 'Admin updated successfully'];
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

// Get admin statistics
$adminStats = $adminModel->getAdminStatistics();
$admins = $adminModel->getAllAdmins();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - Glamour Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Circular Std', 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #E3F2FD 0%, #B3E5FC 50%, #81D4FA 100%); 
            min-height: 100vh; 
            color: #3E2723; 
            display: flex; 
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-right: 1px solid rgba(255, 255, 255, 0.3);
            padding: 30px 0;
            box-shadow: 5px 0 25px rgba(62, 39, 35, 0.1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 0 30px 30px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
            margin-bottom: 30px;
        }

        .sidebar-logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #3E2723;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-logo i {
            color: #FF6B6B;
        }

        .nav-section {
            margin-bottom: 30px;
        }

        .nav-section-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #8D6E63;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            padding: 0 30px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 30px;
            color: #5D4037;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background: rgba(255, 107, 107, 0.1);
            color: #FF6B6B;
            border-left-color: #FF6B6B;
        }

        .nav-item.active {
            background: rgba(255, 107, 107, 0.1);
            color: #FF6B6B;
            border-left-color: #FF6B6B;
        }

        .logout-btn {
            position: absolute;
            bottom: 30px;
            left: 30px;
            right: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 20px;
            background: linear-gradient(135deg, #FF6B6B, #FF8E8E);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.3);
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
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #3E2723;
        }

        .add-admin-btn {
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

        .add-admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(76, 175, 80, 0.3);
        }

        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #FF6B6B;
            margin-bottom: 5px;
        }

        .stat-card p {
            color: #8D6E63;
            font-weight: 500;
        }

        /* Admin Table */
        .admin-table-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            background: rgba(255, 107, 107, 0.1);
            padding: 20px;
            text-align: left;
            font-weight: 600;
            color: #3E2723;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
        }

        .admin-table td {
            padding: 20px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.05);
        }

        .admin-table tr:hover {
            background: rgba(255, 107, 107, 0.05);
        }

        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FF6B6B, #FF8E8E);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-name {
            font-weight: 600;
            color: #3E2723;
        }

        .admin-email {
            color: #8D6E63;
            font-size: 0.9rem;
        }

        .admin-role {
            background: rgba(255, 107, 107, 0.1);
            color: #FF6B6B;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: rgba(76, 175, 80, 0.1);
            color: #2E7D32;
        }

        .status-inactive {
            background: rgba(244, 67, 54, 0.1);
            color: #C62828;
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
            font-weight: 700;
            color: #3E2723;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #8D6E63;
            cursor: pointer;
            padding: 5px;
        }

        .modal-body {
            padding: 30px;
        }

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
            border-color: #FF6B6B;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
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

        .btn-delete-confirm {
            background: linear-gradient(135deg, #F44336, #EF5350);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        /* Admin Detail Grid */
        .admin-detail-grid {
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

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1001;
                background: white;
                border: none;
                padding: 10px;
                border-radius: 8px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .admin-table {
                font-size: 0.9rem;
            }

            .admin-table th,
            .admin-table td {
                padding: 10px;
            }
        }

        .mobile-menu-btn {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <?php include 'includes/admin-sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Manage Admins</h1>
            <button class="add-admin-btn" onclick="showCreateAdminModal()">
                <i class="fas fa-plus"></i>
                Add Admin
            </button>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $adminStats['total']; ?></h3>
                <p>Total Admins</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $adminStats['active']; ?></h3>
                <p>Active Admins</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $adminStats['inactive']; ?></h3>
                <p>Inactive Admins</p>
            </div>
        </div>

        <!-- Admins Table -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td>
                            <div class="admin-info">
                                <div class="admin-avatar">
                                    <?php echo strtoupper(substr($admin['username'] ?? $admin['name'] ?? 'A', 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="admin-name"><?php echo htmlspecialchars($admin['username'] ?? $admin['name'] ?? 'Unknown'); ?></div>
                                    <div class="admin-email"><?php echo htmlspecialchars($admin['email'] ?? ''); ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="admin-role"><?php echo htmlspecialchars($admin['role'] ?? 'admin'); ?></span>
                        </td>
                        <td>
                            <span class="status-badge status-active">
                                <?php echo ucfirst($admin['status'] ?? 'active'); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            $createdDate = $admin['created_at'] ?? $admin['createdAt'] ?? null;
                            if ($createdDate) {
                                if (is_numeric($createdDate)) {
                                    echo date('M j, Y', $createdDate / 1000);
                                } else {
                                    echo date('M j, Y', strtotime($createdDate));
                                }
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td>
                                                         <div class="action-buttons">
                                 <button class="btn btn-view" onclick="viewAdmin('<?php echo $admin['_id']; ?>')" title="View Details">
                                     <i class="fas fa-eye"></i>
                                 </button>
                                 <button class="btn btn-edit" onclick="editAdmin('<?php echo $admin['_id']; ?>')" title="Edit Admin">
                                     <i class="fas fa-edit"></i>
                                 </button>
                                 <?php if (($admin['status'] ?? 'active') === 'active'): ?>
                                     <button class="btn btn-toggle" onclick="toggleAdminStatus('<?php echo $admin['_id']; ?>', 'inactive')" title="Deactivate Admin">
                                         <i class="fas fa-ban"></i>
                                     </button>
                                 <?php else: ?>
                                     <button class="btn btn-toggle" onclick="toggleAdminStatus('<?php echo $admin['_id']; ?>', 'active')" title="Activate Admin">
                                         <i class="fas fa-check"></i>
                                     </button>
                                 <?php endif; ?>
                                 <button class="btn btn-delete" onclick="deleteAdmin('<?php echo $admin['_id']; ?>', '<?php echo htmlspecialchars($admin['username'] ?? $admin['name'] ?? 'Unknown'); ?>')" title="Delete Admin">
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

    <!-- Create Admin Modal -->
    <div id="createAdminModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Create New Admin</h3>
                <button class="modal-close" onclick="closeModal('createAdminModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="createAdminForm">
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
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-select" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('createAdminModal')">Cancel</button>
                    <button type="submit" class="btn-save">Create Admin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div id="editAdminModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Admin</h3>
                <button class="modal-close" onclick="closeModal('editAdminModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editAdminForm">
                <input type="hidden" name="admin_id" id="editAdminId">
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
                        <label class="form-label">Role *</label>
                        <select name="role" id="editRole" class="form-select" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" id="editFullName" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" id="editPhone" class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('editAdminModal')">Cancel</button>
                    <button type="submit" class="btn-save">Update Admin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Admin Modal -->
    <div id="viewAdminModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Admin Details</h3>
                <button class="modal-close" onclick="closeModal('viewAdminModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="viewAdminModalBody">
                <!-- Admin details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteAdminModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Delete</h3>
                <button class="modal-close" onclick="closeModal('deleteAdminModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the admin "<span id="deleteAdminName"></span>"?</p>
                <p style="color: #F44336; font-weight: 600;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('deleteAdminModal')">Cancel</button>
                <form method="POST" id="deleteAdminForm" style="display: inline;">
                    <input type="hidden" name="action" value="delete_admin">
                    <input type="hidden" name="admin_id" id="deleteAdminId">
                    <button type="submit" class="btn-delete-confirm">Delete Admin</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }

        function showCreateAdminModal() {
            document.getElementById('createAdminModal').classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function viewAdmin(adminId) {
            // Fetch admin details and show in modal
            fetch('manage-admins.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_admin_details&admin_id=' + adminId + '&ajax=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const admin = data.data;
                    const modalBody = document.getElementById('viewAdminModalBody');
                    
                    modalBody.innerHTML = `
                        <div class="admin-detail-grid">
                            <div class="detail-item">
                                <div class="detail-label">Username</div>
                                <div class="detail-value">${admin.username || admin.name || 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Email</div>
                                <div class="detail-value">${admin.email || 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Role</div>
                                <div class="detail-value">${admin.role || 'admin'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Status</div>
                                <div class="detail-value">
                                    <span class="status-badge status-${admin.status || 'active'}">${(admin.status || 'active').charAt(0).toUpperCase() + (admin.status || 'active').slice(1)}</span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Full Name</div>
                                <div class="detail-value">${admin.full_name || 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Phone</div>
                                <div class="detail-value">${admin.phone || 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Created</div>
                                <div class="detail-value">${admin.created_at ? new Date(admin.created_at).toLocaleString() : 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Last Updated</div>
                                <div class="detail-value">${admin.updated_at ? new Date(admin.updated_at).toLocaleString() : 'N/A'}</div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('viewAdminModal').classList.add('active');
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

                 function editAdmin(adminId) {
             // Fetch admin details and populate form
             fetch('manage-admins.php', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/x-www-form-urlencoded',
                 },
                 body: 'action=get_admin_details&admin_id=' + adminId + '&ajax=1'
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     const admin = data.data;
                     document.getElementById('editAdminId').value = admin._id;
                     document.getElementById('editUsername').value = admin.username || admin.name || '';
                     document.getElementById('editEmail').value = admin.email || '';
                     document.getElementById('editRole').value = admin.role || 'admin';
                     document.getElementById('editFullName').value = admin.full_name || '';
                     document.getElementById('editPhone').value = admin.phone || '';
                     document.getElementById('editAdminModal').classList.add('active');
                 } else {
                     alert('Error: ' + data.message);
                 }
             });
         }

        function toggleAdminStatus(adminId, newStatus) {
            if (confirm('Are you sure you want to ' + newStatus + ' this admin?')) {
                fetch('manage-admins.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=update_status&admin_id=' + adminId + '&status=' + newStatus + '&ajax=1'
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

        function deleteAdmin(adminId, adminName) {
            if (confirm('Are you sure you want to delete the admin "' + adminName + '"? This action cannot be undone.')) {
                fetch('manage-admins.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=delete_admin&admin_id=' + adminId + '&ajax=1'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Admin deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        // Form submissions
        document.getElementById('createAdminForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'create_admin');
            formData.append('ajax', '1');

            fetch('manage-admins.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Admin created successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });

        document.getElementById('editAdminForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_admin');
            formData.append('ajax', '1');

            fetch('manage-admins.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Admin updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>

    <!-- Username Validation CSS -->
    <link rel="stylesheet" href="../styles/username-validation.css">

    <!-- Username Validation JavaScript -->
    <script src="../scripts/username-validation.js"></script>
</body>
</html>
