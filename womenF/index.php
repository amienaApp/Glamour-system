<?php
$page_title = 'Galamor palace';

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Set page title based on subcategory
if ($subcategory) {
    $page_title = ucfirst($subcategory) . ' - ' . $page_title;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo isset($page_title) ? $page_title : 'Lulus - Women\'s Clothing & Fashion'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../heading/header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/sidebar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/main.css?v=<?php echo time(); ?>">
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
</head>
<body>
                    <?php include '../heading/header.php'; ?>

                <!-- Image Bar Section -->
                <div class="image-bar">
                    <a href="index.php" class="image-item">
                        <img src="../img/women/dresses/12.webp" alt="Women Fashion 12">
                        <h3>Shop All</h3>
                    </a>
                    <a href="index.php?subcategory=dresses" class="image-item">
                        <img src="../img/women/13.webp" alt="Women Fashion 13">
                        <h3>Dresses</h3>
                    </a>
                    <a href="index.php?subcategory=wedding-guest" class="image-item">
                        <img src="../img/women/14.avif" alt="Women Fashion 14">
                        <h3>Wedding Guest</h3>
                    </a>
                    <a href="index.php?subcategory=wedding-dress" class="image-item">
                        <img src="../img/women/dresses/17.webp" alt="Women Fashion 17">
                        <h3>Wedding-dress</h3>
                    </a>
                    <a href="index.php?subcategory=abaya" class="image-item">
                        <img src="../img/women/NEW/11.webp" alt="Women Fashion 12">
                        <h3>Abaya</h3>
                    </a>
                    <a href="index.php?subcategory=summer-dresses" class="image-item">
                        <img src="../img/women/dresses/20.1.webp" alt="Women Fashion 13">
                        <h3>Summer-dresses</h3>
                    </a>
                    <a href="index.php?subcategory=homecoming" class="image-item">
                        <img src="../img/women/14.avif" alt="Women Fashion 14">
                        <h3>Homecoming</h3>
                    </a>
                </div>

                <div class="page-layout">
                    <?php include 'includes/sidebar.php'; ?>
                    <?php include 'includes/main-content.php'; ?>
                </div>

</body>
</html> 