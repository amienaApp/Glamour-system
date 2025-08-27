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
                    <a href="shoes.php" class="image-item">
                        <img src="../img/shoes/1.webp" alt="All Shoes">
                        <h3>Shop All</h3>
                    </a>
                    <a href="shoes.php?subcategory=menshoes" class="image-item">
                        <img src="../img/shoes/menshoes/1.0.jpg" alt="Men's Shoes">
                        <h3>Men's Shoes</h3>
                    </a>
                    <a href="shoes.php?subcategory=womenshoes" class="image-item">
                        <img src="../img/shoes/womenshoes/1.0.avif" alt="Women's Shoes">
                        <h3>Women's Shoes</h3>
                    </a>
                    <a href="shoes.php?subcategory=childrenshoes" class="image-item">
                        <img src="../img/shoes/boy/1.0.avif" alt="Children's Shoes">
                        <h3>Children's Shoes</h3>
                    </a>
                    <a href="shoes.php?subcategory=sportsshoes" class="image-item">
                        <img src="../img/shoes/menshoes/1.1.0.jpg" alt="Sports Shoes">
                        <h3>Sports Shoes</h3>
                    </a>
                    <a href="shoes.php?subcategory=formalshoes" class="image-item">
                        <img src="../img/shoes/menshoes/1.1.jpg" alt="Formal Shoes">
                        <h3>Formal Shoes</h3>
                    </a>
                    <a href="shoes.php?subcategory=casualshoes" class="image-item">
                        <img src="../img/shoes/womenshoes/1.0.webp" alt="Casual Shoes">
                        <h3>Casual Shoes</h3>
                    </a>
                </div>

                <div class="page-layout">
                    <?php include 'includes/sidebar.php'; ?>
                    <?php include 'includes/main-content.php'; ?>
                </div>

</body>
</html> 