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
            <div class="nav-section-title">System</div>
            <a href="../index.html" class="nav-item">
                <i class="fas fa-store"></i>
                View Store
            </a>
        </div>
    </nav>

    <?php if (in_array($currentPage, ['add-product.php', 'edit-product.php', 'manage-categories.php'])): ?>
    <div class="sidebar-actions">
        <a href="add-product.php" class="sidebar-action-btn">
            <i class="fas fa-plus"></i> Add Product
        </a>
        <a href="manage-products.php" class="sidebar-action-btn secondary">
            <i class="fas fa-boxes"></i> Manage Products
        </a>
        <a href="manage-categories.php" class="sidebar-action-btn success">
            <i class="fas fa-folder"></i> Manage Categories
        </a>
    </div>
    <?php endif; ?>
    
    <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        Logout
    </a>
</div>




