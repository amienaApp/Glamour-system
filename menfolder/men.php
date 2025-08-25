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
                <div class="image-bar" >
                    <a href="men.php" class="image-item">
                        <img src="../img/menn/men.jpg" alt="Men's Fashion">
                        <h3>Shop All</h3>
                    </a>
                    <a href="men.php?subcategory=shirts" class="image-item">
                        <img src="../img//men/shirts/14.jpg" alt="men Fashion 13">
                        <h3>Shirts</h3>
                    </a>
                    <a href="men.php?subcategory=tshirts" class="image-item">
                        <img src="../img/men/t-shirts/6.2.png" alt="men Fashion 14">
                        <h3>T-Shirts</h3>
                    </a>
                    <a href="men.php?subcategory=suits" class="image-item">
                        <img src="../img/men/suits/5.6.jpg" alt="suit formal">
                        <h3>Suits</h3>
                    </a>
                    <a href="men.php?subcategory=pants" class="image-item">
                        <img src="../img/men/pants/9.1.jpg" alt=" pants">
                        <h3>Pants</h3>
                    </a>
                    <a href="men.php?subcategory=shorts" class="image-item">
                        <img src="../img/men/shorts/5.jpg" alt=" shorts">
                        <h3>Shorts & Underwear</h3>
                    </a>
                    <a href="men.php?subcategory=hoodies" class="image-item">
                        <img src="../img/men/hoodie$sweatshirt/3.jpg" alt=" hoodie">
                        <h3>Hoodies & Sweatshirts</h3>
                    </a>
                </div>

                <div class="page-layout">
                    <?php include 'includes/sidebar.php'; ?>
                    <?php include 'includes/main-content.php'; ?>
                </div>

</body>
</html> 