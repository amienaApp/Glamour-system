<?php
// Get current page for active state
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="sidebar-logo">
            <i class="fas fa-gem"></i>
            Glamour Admin
        </a>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Dashboard</div>
            <a href="index.php" class="nav-item <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Products</div>
            <a href="add-product.php" class="nav-item <?php echo $currentPage === 'add-product.php' ? 'active' : ''; ?>">
                <i class="fas fa-plus"></i>
                Add Product
            </a>
            <a href="view-products.php" class="nav-item <?php echo $currentPage === 'view-products.php' ? 'active' : ''; ?>">
                <i class="fas fa-eye"></i>
                View Products
            </a>
            <a href="manage-products.php" class="nav-item <?php echo $currentPage === 'manage-products.php' ? 'active' : ''; ?>">
                <i class="fas fa-boxes"></i>
                Manage Products
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Categories</div>
            <a href="manage-categories.php" class="nav-item <?php echo $currentPage === 'manage-categories.php' ? 'active' : ''; ?>">
                <i class="fas fa-folder"></i>
                Manage Categories
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Orders & Payments</div>
            <a href="manage-orders.php" class="nav-item <?php echo $currentPage === 'manage-orders.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i>
                Manage Orders
            </a>
            <a href="manage-payments.php" class="nav-item <?php echo $currentPage === 'manage-payments.php' ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i>
                Manage Payments
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Users & Admins</div>
            <a href="manage-users.php" class="nav-item <?php echo $currentPage === 'manage-users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                Manage Users
            </a>
            <a href="manage-admins.php" class="nav-item <?php echo $currentPage === 'manage-admins.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-shield"></i>
                Manage Admins
            </a>
            <a href="manage-feedback.php" class="nav-item <?php echo $currentPage === 'manage-feedback.php' ? 'active' : ''; ?>">
                <i class="fas fa-comments"></i>
                Manage Feedback
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">System</div>
            <a href="../index.php" class="nav-item" target="_blank">
                <i class="fas fa-store"></i>
                View Store
            </a>
        </div>
    </nav>

    <!-- Logout button at the very end -->
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </div>
</div>




