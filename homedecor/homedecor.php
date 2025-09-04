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
            <link rel="stylesheet" href="styles/featured-products.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="styles/quick-view.css?v=<?php echo time(); ?>">
        <script src="script.js?v=<?php echo time(); ?>" defer></script>
        <script src="js/filtering.js?v=<?php echo time(); ?>" defer></script>
        <script src="js/quick-view.js?v=<?php echo time(); ?>" defer></script>
</head>
<body>
                    <?php include '../heading/header.php'; ?>

                <!-- Image Bar Section -->
                <div class="image-bar" >
                    <a href="homedecor.php" class="image-item">
                        <img src="../img/home-decor1.webp" alt="Home Decor">
                        <h3>Shop All</h3>
                    </a>
                    <a href="homedecor.php?subcategory=bedding" class="image-item">
                        <img src="../img/home-decor/bedroom/7.jpg" alt="Bedding">
                        <h3>Bedding</h3>
                    </a>
                    <a href="homedecor.php?subcategory=dinningroom" class="image-item">
                        <img src="../img/home-decor/diningarea/4.jpg" alt="Dining Room">
                        <h3>Dining Room</h3>
                    </a>
                    <a href="homedecor.php?subcategory=kitchen" class="image-item">
                        <img src="../img/home-decor/kitchen/8.jpg" alt="Kitchen">
                        <h3>Kitchen</h3>
                    </a>
                    <a href="homedecor.php?subcategory=lightinnig" class="image-item">
                        <img src="../img/home-decor/light/3.webp" alt="Lighting">
                        <h3>Lighting</h3>
                    </a>
                    <a href="homedecor.php?subcategory=artwork" class="image-item">
                        <img src="../img/home-decor/artwork/21.jpg" alt="Artwork">
                        <h3>Artwork</h3>
                    </a>
                    <a href="homedecor.php?subcategory=livingroom" class="image-item">
                        <img src="../img/home-decor/livingroom/8.jpg" alt="Living Room">
                        <h3>Living Room</h3>
                    </a>
                </div>

                <div class="page-layout">
                    <?php include 'includes/sidebar.php'; ?>
                    <?php include 'includes/main-content.php'; ?>
                </div>

</body>
</html> 