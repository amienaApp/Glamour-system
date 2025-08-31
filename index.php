<?php
session_start();

// Include database connection and models
require_once 'config/mongodb.php';
require_once 'models/Product.php';

// Initialize product model
$productModel = new Product();

// Get featured products (or all products if no featured ones)
$featuredProducts = $productModel->getFeatured();
if (empty($featuredProducts)) {
    // If no featured products, get some products with variants
    $featuredProducts = $productModel->getAll(['color_variants' => ['$exists' => true, '$ne' => []]], [], 12);
}
if (empty($featuredProducts)) {
    // If still empty, get any products
    $featuredProducts = $productModel->getAll([], [], 12);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glamor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- Include the home header CSS -->
    <link rel="stylesheet" href="heading/home-header.css?v=<?php echo time(); ?>">
     
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
   
</head>
<body>
    
    <!-- Include the home header -->
    <?php include 'heading/home-header.php'; ?>
     
   

<!-- Chat Button -->
<div class="chat-button">
    <i class="fas fa-comments"></i>
</div> 


    <section class="banner-swiper-section">
        <div class="swiper banner-swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="img/banner9.webp" alt="Banner 3">
                </div>
                
                <div class="swiper-slide">
                    <img src="img/banner14.webp" alt="Banner 4">
                </div>
                
                <div class="swiper-slide">
                    <img src="img/banner5.png" alt="Banner 5">
                </div>
                
                <div class="swiper-slide">
                    <img src="img/banner6.jpg" alt="Banner 6">
                </div>
                1
                <div class="swiper-slide">
                    <img src="img/banner7.jpg" alt="Banner 7">
                </div>
                
                <div class="swiper-slide">
                    <img src="img/banner8.jpg" alt="Banner 8">
                </div>
            </div>
            
            <!-- Navigation arrows -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            
            <!-- Pagination dots -->
            <div class="swiper-pagination"></div>
        </div>
    </section>
    
     

         
         

         <!-- Shop by category -->
    <section  class="product container spacing">
      <div class="title"> 
         <h2>Shop by category</h2>
        <P>Explore our curated collections designed for every style and occasion</P>
    </div>
     



        <div class="pro-container container">

        <div class="card">
                 <img src="img/menn/men.jpg" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Men's Collection</h3>
                     <P>Trending style & Timeless Classic</P>   
                                             <a href="menfolder/men.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>





                             <div class="card">
                 <img src="img/women/1.jpg" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Women's Collection</h3>
                     <P>Trending style & Timeless Classic</P>   
                      <a href="womenF/index.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>



                             <div class="card">
                 <img src="img/child/child.webp" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Children's Collection</h3>
                     <P>Trending style & Timeless Classic</P>   
                      <a href="childrenfolder/children.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>



                             <div class="card">
                 <img src="img/shoes/1.webp" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Shoes Collection</h3>
                     <P>Trending style & Timeless Classic</P>   
                      <a href="shoess/shoes.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>



                             <div class="card">
                 <img src="img/bags/1.avif" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Bags Collection</h3>
                     <P>Trending style & Timeless Classic</P>   
                      <a href="bagsfolder/bags.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>



                             <div class="card">
                 <img src="img/perfumes/15.jpg" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Perfumes Collection</h3>
                     <P>Trending style & Timeless Classic</P>   
                      <a href="perfumes/index.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>



                             <div class="card">
                 <img src="img/accessories/1.jpeg" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Accessories Collection</h3>
                     <P>Trending style & Timeless Classic</P>   
                      <a href="accessories/accessories.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>

              



                                                               <div class="card">
                 <img src="img/accessories/1.jpeg" cover / center no-repeat>
                 <div class="card-content">
                    <h3> Jewelry</h3>
                     <P>Trending style & Timeless Classic</P>   
                      <a href="accessories/accessories.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>


           </div>

       </section>

               <!-- Laci Kaye Booth Edit Section -->
   <section class="laci-section">
     <!-- Left: Large Feature Image with Text Overlay -->
     <div class="laci-left">
           <div class="category4">
             <a href="womenF/index.php?category=dresses">
               <video src="./img/sawiro/dressvideo.mp4" autoplay loop muted controls> </video>
               <div class="overlay4">Wedding Dresses</div>
             </a>
           </div>
           </div>
      
      
       <!-- center -->
       <div class="gallery">
         <div class="container4">
             <div class="polaroid">
                <a href="womenF/index.php?category=dresses">
                   <img src="./img/sawiro/9.jpg" alt="Photo 1">
                </a>
             </div>

             <div class="polaroid">
                <a href="womenF/index.php?category=dresses">
                   <img src="./img/sawiro/8.jpg" alt="Photo 2">
                </a>
             </div>
             

             <div class="polaroid">
                <a href="womenF/index.php?category=dresses">
                   <img src="./img/sawiro/10.jpg" alt="Photo 3">
                </a>
             </div>

             <div class="polaroid">
                <a href="accessories/accessories.php?category=sunglasses">
                   <img src="./img/accessories/men/sunglasses/7.jpg" alt="Photo 4">
                </a>
             </div>

             <div class="polaroid">
                <a href="womenF/index.php?category=dresses">
                   <img src="./img/sawiro/7.jpg" alt="Photo 5">
                </a>
             </div>

             <div class="polaroid">
                <a href="womenF/index.php?category=dresses">
                   <img src="./img/sawiro/21.webp" alt="Photo 6">
                </a>
             </div>

             <div class="polaroid">
               <a href="shoess/men.php?category=children">
                  <img src="./img/sawiro/kidshoe.jpg" alt="Photo 7">
               </a>
             </div>

             <div class="polaroid">
               <a href="womenF/index.php?category=dresses">
                  <img src="./img/sawiro/12.jpg" alt="Photo 8">
                </a>
             </div>

             <div class="polaroid">
               <a href="womenF/index.php?category=dresses">
                  <img src="./img/sawiro/13.jpg" alt="Photo 9">
               </a>
             </div>

   

             
             
         </div>
      </div>
        
        
     <!-- Right: Promo with Button -->
     <div class="laci-right">
           <div class="category1">
                 <a href="menfolder/men.php">
                   <video src="./img/sawiro/manvideo.mp4" autoplay loop muted controls> </video>
                 </a>
           
               </div>
          
           <div class="laci-right-content">
             <p>Classic Style</p>
             <a href="menfolder/men.php">Shop Now</a>
       </div>  
      </section>

           <!-- section contains only pictures and some videos -->
   <section>
   <div class="container5" >
       <div class="category5">
         <a href="menfolder/men.php?category=suits">
           <video src="./img/sawiro/suitvideo.mp4" autoplay loop muted controls> </video>
           <div class="overlay5">suit Dresses</div>
         </a>
       </div>
      
       <div class="category5">
         <a href="womenF/index.php?category=dresses">
           <img src="./img/sawiro/22.webp" alt="Formal Dresses">
           <div class="overlay5">Formal Dresses</div>
         </a>
       </div>

         <div class="category5">
       <a href="womenF/index.php?category=dresses">
         <video src="./img/sawiro/taash.mp4" autoplay loop muted controls> </video>
         <div class="overlay5">Bride Dresses</div>
       </a>
       </div>
       
       <div class="category5">
         <a href="womenF/index.php?category=beauty">
           <video src="./img/sawiro/makupvideo.mp4" autoplay loop muted controls ></video>
           <div class="overlay5">makeup</div>
         </a>
       </div>

       <div class="category5">
         <a href="bagsfolder/bags.php">
           <img src="./img/sawiro/handbag1.jpg" alt="Formal Dresses">
           <div class="overlay5">Handbag</div>
         </a>
       </div>

       <div class="category5">
         <a href="womenF/index.php?category=dresses">
           <video src="./img/sawiro/dressvideo2.mp4" autoplay loop muted controls ></video>
           <div class="overlay5">Formal Dresses</div>
         </a>
       </div>
  

       <div class="category5">
        <a href="shoess/men.php">
         <video src="./img/sawiro/shoesvideo.mp4" autoplay loop muted controls ></video>
         <div class="overlay5">Casual Shoes</div>
        </a>
       </div>

       <div class="category5">
         <a href="accessories/accessories.php?category=watches">
           <video src="./img/sawiro/watch.mp4" autoplay loop muted controls ></video>
           <div class="overlay5">Watches</div>
         </a>
       </div>

       <div class="category5">
         <a href="accessories/accessories.php?category=jewelry">
           <img src="./img/sawiro/jwel2.jpg" alt="Formal Dresses">
           <div class="overlay5">Jewelry</div>
         </a>
       </div>

       
   </div>
    </section>
  
   <!-- featured products -->
    



<!-- featured products Section -->
<main class="main-content">
    <div class="content-header" >
        <h1 class="page-title">Featured Products</h1>
        <div class="content-controls">
            <div class="sort-control">
                <label for="sort-select">Sort:</label>
                <select id="sort-select" class="sort-select">
                    <option value="featured" selected>Featured</option>
                    <option value="newest">Newest</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="popular">Most Popular</option>
                </select>
            </div>
            <div class="view-control">
                <span>View:</span>
                <a href="#" class="view-option active">60</a>
                <span>|</span>
                <a href="#" class="view-option">120</a>
            </div>
        </div>
    </div>
    

    <div class="product-grid" data-category="featured">
        <?php 
        if (!empty($featuredProducts)): ?>
            <?php foreach ($featuredProducts as $product): ?>
                <?php 
                $variantCount = isset($product['color_variants']) ? count($product['color_variants']) : 0;
                ?>
                <div class="product-card" data-product-id="<?php echo $product['_id']; ?>">
                    <div class="product-image">
                        <div class="image-slider">
                            <?php 
                            $displayImage = '';
                            $colorVariants = [];
                            
                            // Try to get images from color variants first
                            if (isset($product['color_variants']) && !empty($product['color_variants'])) {
                                $colorVariants = (array)$product['color_variants'];
                                if (!empty($colorVariants)) {
                                    $firstVariant = $colorVariants[0];
                                    if (isset($firstVariant['front_image']) && !empty($firstVariant['front_image'])) {
                                        $displayImage = $firstVariant['front_image'];
                                    }
                                }
                            }
                            
                            // Fallback to front_image if no color variant images
                            if (empty($displayImage)) {
                                $displayImage = $product['front_image'] ?? $product['image_front'] ?? '';
                            }
                            
                            if (!empty($displayImage)) {
                                $extension = pathinfo($displayImage, PATHINFO_EXTENSION);
                                if (in_array(strtolower($extension), ['mp4', 'webm', 'mov'])): ?>
                                    <video src="<?php echo htmlspecialchars($displayImage); ?>" 
                                           alt="<?php echo htmlspecialchars($product['name']); ?> - Front" 
                                           class="active" 
                                           data-color="default"
                                           muted
                                           loop>
                                    </video>
                                <?php else: ?>
                                    <img src="<?php echo htmlspecialchars($displayImage); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?> - Front" 
                                         class="active" 
                                         data-color="default">
                                <?php endif; ?>
                            <?php } ?>
                            
                            <?php 
                            // Display color variant images
                            if (!empty($colorVariants)):
                                foreach ($colorVariants as $variant):
                                    $variantFrontImage = $variant['front_image'] ?? '';
                                    $variantBackImage = $variant['back_image'] ?? '';
                                    
                                    // If no back image for variant, use front image for both
                                    if (empty($variantBackImage) && !empty($variantFrontImage)) {
                                        $variantBackImage = $variantFrontImage;
                                    }
                                    
                                    if ($variantFrontImage): 
                                        $variantFrontExtension = pathinfo($variantFrontImage, PATHINFO_EXTENSION);
                                        if (in_array(strtolower($variantFrontExtension), ['mp4', 'webm', 'mov'])): ?>
                                            <video src="<?php echo htmlspecialchars($variantFrontImage); ?>" 
                                                   alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                                   data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                   muted
                                                   loop>
                                            </video>
                                        <?php else: ?>
                                            <img src="<?php echo htmlspecialchars($variantFrontImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                                 data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if ($variantBackImage): 
                                        $variantBackExtension = pathinfo($variantBackImage, PATHINFO_EXTENSION);
                                        if (in_array(strtolower($variantBackExtension), ['mp4', 'webm', 'mov'])): ?>
                                            <video src="<?php echo htmlspecialchars($variantBackImage); ?>" 
                                                   alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                                   data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                   muted
                                                   loop>
                                            </video>
                                        <?php else: ?>
                                            <img src="<?php echo htmlspecialchars($variantBackImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                                 data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="heart-button">
                            <i class="fas fa-heart"></i>
                        </button>
                        <div class="product-actions">
                            <button class="quick-view" data-product-id="<?php echo $product['_id']; ?>">Quick View</button>
                            <button class="add-to-bag">Add To Bag</button>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="color-options">
                            <?php 
                            // Main product color
                            if (!empty($product['color'])): ?>
                                <span class="color-circle active" 
                                      style="background-color: <?php echo htmlspecialchars($product['color']); ?>;" 
                                      title="<?php echo htmlspecialchars($product['color']); ?>" 
                                      data-color="<?php echo htmlspecialchars($product['color']); ?>"></span>
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant colors
                            if (!empty($colorVariants)):
                                foreach ($colorVariants as $variant):
                                    if (!empty($variant['color'])): ?>
                                        <span class="color-circle" 
                                              style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;" 
                                              title="<?php echo htmlspecialchars($variant['name']); ?>" 
                                              data-color="<?php echo htmlspecialchars($variant['color']); ?>"></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price">$<?php echo number_format($product['price'], 0); ?></div>
                        <?php if (($product['available'] ?? true) === false): ?>
                            <div class="out-of-stock">Out of Stock</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products">
                <p>No featured products found.</p>
            </div>
        <?php endif; ?>
    </div>

   

        
    </div>

</main>

<!-- Quick View Sidebar -->
<div class="quick-view-sidebar" id="quick-view-sidebar">
    <div class="quick-view-header">
        <button class="close-quick-view" id="close-quick-view">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="quick-view-content">
        <!-- Product Images -->
        <div class="quick-view-images">
            <div class="main-image-container">
                <img id="quick-view-main-image" src="" alt="Product Image">
            </div>
            <div class="thumbnail-images" id="quick-view-thumbnails">
                <!-- Thumbnails will be populated by JavaScript -->
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="quick-view-details">
            <h2 id="quick-view-title"></h2>
            <div class="quick-view-price" id="quick-view-price"></div>
            <div class="quick-view-reviews">
                <span class="stars">★★★★★</span>
                <span class="review-count">(0 Reviews)</span>
            </div>
            
            <!-- Color Selection -->
            <div class="quick-view-colors">
                <h4>Color</h4>
                <div class="color-selection" id="quick-view-color-selection">
                    <!-- Colors will be populated by JavaScript -->
                </div>
            </div>
            
            <!-- Size Selection -->
            <div class="quick-view-sizes">
                <h4>Size</h4>
                <div class="size-selection" id="quick-view-size-selection">
                    <!-- Sizes will be populated by JavaScript -->
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="quick-view-actions">
                <button class="add-to-bag-quick" id="add-to-bag-quick">
                    <i class="fas fa-shopping-bag"></i>
                    Add to Bag
                </button>
                <button class="add-to-wishlist-quick" id="add-to-wishlist-quick">
                    <i class="fas fa-heart"></i>
                    + Wishlist
                </button>
            </div>
            
            <!-- Product Description -->
            <div class="quick-view-description">
                <p></p>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Overlay -->
<div class="quick-view-overlay" id="quick-view-overlay"></div> 

  <!-- Footer Start -->
    <footer class="custom-footer">
        <div class="footer-container">
        <div class="footer-col">
            <h3>Useful Links</h3>
            <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">Categories</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">pagedetails</a></li>
            <li><a href="#">Contact us</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h3>About us</h3>
            <p>We are a team of passionate people whose goal is to improve everyone's life through disruptive products. We build great products to solve your business problems.</p>
            <p>Our products are designed for small to medium size companies willing to optimize their performance.</p>
        </div>
        <div class="footer-col">
            <h3>Connect with us</h3>
            <ul class="footer-contact">
            <li><span class="footer-icon">💬</span> Contact us</li>
            <li><span class="footer-icon">✉</span> Glamor@gmail.com</li>
            <li><span class="footer-icon">📞</span> +252907166125 $252906041037 </li>
            </ul>
            <div class="footer-social">
            <a href="https://wa.me/252907166125" target="_blank" title="WhatsApp">
                <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.472-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.447-.52.151-.174.2-.298.3-.497.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.571-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.077 4.363.709.306 1.262.489 1.694.626.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.288.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.617h-.001a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.999-3.648-.235-.374A9.86 9.86 0 012.1 12.045C2.073 6.507 6.659 2 12.207 2c2.654 0 5.151 1.037 7.032 2.918a9.825 9.825 0 012.929 7.029c-.003 5.538-4.589 10.045-10.147 10.045m8.413-18.457A11.815 11.815 0 0012.207 0C5.475 0 .073 5.373.1 12.06c.021 2.13.557 4.21 1.611 6.077L.057 24l6.084-1.602a11.888 11.888 0 005.408 1.378h.005c6.729 0 12.207-5.373 12.234-12.06a11.82 11.82 0 00-3.48-8.456"/></svg>
            </a>
            <a href="https://instagram.com/" target="_blank" title="Instagram">
                <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.974.974 1.246 2.241 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.974.974-2.241 1.246-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.974-.974-1.246-2.241-1.308-3.608C2.175 15.647 2.163 15.267 2.163 12s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608C4.515 2.567 5.782 2.295 7.148 2.233 8.414 2.175 8.794 2.163 12 2.163zm0-2.163C8.741 0 8.332.013 7.052.072 5.771.131 4.659.363 3.678 1.344c-.98.98-1.213 2.092-1.272 3.373C2.013 5.668 2 6.077 2 12c0 5.923.013 6.332.072 7.613.059 1.281.292 2.393 1.272 3.373.98.98 2.092 1.213 3.373 1.272C8.332 23.987 8.741 24 12 24s3.668-.013 4.948-.072c1.281-.059 2.393-.292 3.373-1.272.98-.98 1.213-2.092 1.272-3.373.059-1.281.072-1.69.072-7.613 0-5.923-.013-6.332-.072-7.613-.059-1.281-.292-2.393-1.272-3.373-.98-.98-2.092-1.213-3.373-1.272C15.668.013 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zm0 10.162a3.999 3.999 0 110-7.998 3.999 3.999 0 010 7.998zm6.406-11.845a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z"/></svg>
            </a>
            <a href="https://twitter.com/" target="_blank" title="Twitter">
                <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557a9.83 9.83 0 01-2.828.775 4.932 4.932 0 002.165-2.724c-.951.564-2.005.974-3.127 1.195a4.916 4.916 0 00-8.38 4.482C7.691 8.095 4.066 6.13 1.64 3.161c-.542.929-.856 2.01-.857 3.17 0 2.188 1.115 4.117 2.823 5.254a4.904 4.904 0 01-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.936 4.936 0 01-2.224.084c.627 1.956 2.444 3.377 4.6 3.417A9.867 9.867 0 010 21.543a13.94 13.94 0 007.548 2.209c9.142 0 14.307-7.721 13.995-14.646A9.936 9.936 0 0024 4.557z"/></svg>
            </a>
            <a href="https://www.tiktok.com/" target="_blank" title="TikTok">
                <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M12.004 2.003c0-.553.447-1 1-1h2.001c.553 0 1 .447 1 1v2.001c0 .553-.447 1-1 1h-2.001c-.553 0-1-.447-1-1V2.003zm6.001 2.001c0-.553.447-1 1-1h2.001c.553 0 1 .447 1 1v2.001c0 .553-.447 1-1 1h-2.001c-.553 0-1-.447-1-1V4.004zm-12.002 0c0-.553.447-1 1-1h2.001c.553 0 1 .447 1 1v2.001c0 .553-.447 1-1 1h-2.001c-.553 0-1-.447-1-1V4.004zm6.001 2.001c0-.553.447-1 1-1h2.001c.553 0 1 .447 1 1v2.001c0 .553-.447 1-1 1h-2.001c-.553 0-1-.447-1-1V6.005zm-6.001 0c0-.553.447-1 1-1h2.001c.553 0 1 .447 1 1v2.001c0 .553-.447 1-1 1h-2.001c-.553 0-1-.447-1-1V6.005zm12.002 0c0-.553.447-1 1-1h2.001c.553 0 1 .447 1 1v2.001c0 .553-.447 1-1 1h-2.001c-.553 0-1-.447-1-1V6.005zm-6.001 2.001c0-.553.447-1 1-1h2.001c.553 0 1 .447 1 1v2.001c0 .553-.447 1-1 1h-2.001c-.553 0-1-.447-1-1v-2.001zm-6.001 0c0-.553.447-1 1-1h2.001c.553 0 1 .447 1 1v2.001c0 .553-.447 1-1 1h-2.001c-.553 0-1-.447-1-1v-2.001zm12.002 0c0-.553.447-1 1-1h2.001c.553 0 1 .447 1 1v2.001c0 .553-.447 1-1 1h-2.001c-.553 0-1-.447-1-1v-2.001z"/></svg>
            </a>
            <a href="https://facebook.com/" target="_blank" title="Facebook">
                <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.595 0 0 .592 0 1.326v21.348C0 23.408.595 24 1.326 24H12.82v-9.294H9.692v-3.622h3.127V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.406 24 24 23.408 24 22.674V1.326C24 .592 23.406 0 22.675 0"/></svg>
            </a>
            </div>
        </div>
         
    <!-- Newsletter -->
    <div style="flex:1 1 250px; margin-bottom:20px;">
      <h3 style="margin-bottom:15px; font-weight:bold;">Newsletter</h3>
      <p style="margin-bottom:10px;">Subscribe to our newsletter for latest updates and offers.</p>
      <form action="#" method="post" style="display:flex;">
        <input type="email" placeholder="Your email" style="flex:1; padding:10px; border:none; border-radius:3px 0 0 3px;">
        <button type="submit" style="padding:10px 20px; background-color:#ff6600; border:none; border-radius:0 3px 3px 0; color:#fff; cursor:pointer;">Subscribe</button>
      </form></div>
        </div>
        <div class="last-images"> 
        <img src="./img/pay.jpg">
        <img src="./img/play.jpg">
            <img src="./img/app.jpg">

        </div>
        <div class="footer-bottom">
        Copyright © Glamor palaca
        </div>
    </footer>
  <!-- Footer End -->
  
    
      


     
      <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
     
    <!-- Banner Swiper Initialization -->
    <script>
        // Initialize Banner Swiper
        const bannerSwiper = new Swiper('.banner-swiper', {
            // Enable loop
            loop: true,
            
            // Speed up transitions
            speed: 600,
            
            // Enable autoplay with faster timing
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            
            // Enable pagination
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            
            // Enable navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            
            // Use slide effect instead of fade (faster)
            effect: 'slide',
            
            // Responsive breakpoints
            breakpoints: {
                640: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 1,
                },
                1024: {
                    slidesPerView: 1,
                },
            }
        });
        
        // Handle image loading for faster performance
        document.addEventListener('DOMContentLoaded', function() {
            const bannerImages = document.querySelectorAll('.banner-swiper .swiper-slide img');
            bannerImages.forEach(img => {
                if (img.complete) {
                    img.classList.add('loaded');
                } else {
                    img.addEventListener('load', function() {
                        this.classList.add('loaded');
                    });
                    img.addEventListener('error', function() {
                        console.warn('Failed to load image:', this.src);
                        this.style.display = 'none';
                    });
                }
            });
        });
    </script>

         <script src="scripts/main.js"></script>
     
     <!-- Quick View Functionality -->
     <script>
         document.addEventListener('DOMContentLoaded', function() {


             // Quick View Sidebar Functionality
             const quickViewSidebar = document.getElementById('quick-view-sidebar');
             const quickViewOverlay = document.getElementById('quick-view-overlay');
             const closeQuickView = document.getElementById('close-quick-view');
             const quickViewButtons = document.querySelectorAll('.quick-view');
             
             // Product data for quick view - Updated for featured products
             const productData = {
                 1: {
                     name: "Featured Product 1",
                     price: "$25",
                     images: [
                         { src: "img/sawiro/23.jpg", color: "default" }
                     ],
                     colors: [
                         { name: "Default", value: "default", hex: "#96e0f3ff" }
                     ],
                     sizes: ["XS", "S", "M", "L", "XL", "XXL"],
                     soldOutSizes: []
                 },
                 2: {
                     name: "Featured Product 2",
                     price: "$30",
                     images: [
                         { src: "img/sawiro/24.jpg", color: "default" }
                     ],
                     colors: [
                         { name: "Default", value: "default", hex: "#060606ff" }
                     ],
                     sizes: ["XS", "S", "M", "L", "XL", "XXL"],
                     soldOutSizes: []
                 },
                 3: {
                     name: "Featured Product 3",
                     price: "$28",
                     images: [
                         { src: "img/sawiro/20.webp", color: "default" }
                     ],
                     colors: [
                         { name: "Default", value: "default", hex: "#000" }
                     ],
                     sizes: ["XS", "S", "M", "L", "XL", "XXL"],
                     soldOutSizes: []
                 },
                 4: {
                     name: "Featured Product 4",
                     price: "$35",
                     images: [
                         { src: "img/sawiro/barca.jpg", color: "default" }
                     ],
                     colors: [
                         { name: "Default", value: "default", hex: "#001f3f" }
                     ],
                     sizes: ["XS", "S", "M", "L", "XL", "XXL"],
                     soldOutSizes: []
                 },
                 5: {
                     name: "Designer Glasses",
                     price: "$45",
                     images: [
                         { src: "img/sawiro/glas2.jpg", color: "maroon" },
                         { src: "img/sawiro/glas1.jpg", color: "black" }
                     ],
                     colors: [
                         { name: "Maroon", value: "maroon", hex: "#8B0000" },
                         { name: "Black", value: "black", hex: "#000000" }
                     ],
                     sizes: ["One Size"],
                     soldOutSizes: []
                 },
                 6: {
                     name: "Featured Product 6",
                     price: "$55",
                     images: [
                         { src: "img/sawiro/handbag2.jpg", color: "default" }
                     ],
                     colors: [
                         { name: "Default", value: "default", hex: "#ffffffff" }
                     ],
                     sizes: ["One Size"],
                     soldOutSizes: []
                 },
                 7: {
                     name: "Featured Product 7",
                     price: "$38",
                     images: [
                         { src: "img/sawiro/jwel2.jpg", color: "default" }
                     ],
                     colors: [
                         { name: "Default", value: "default", hex: "#14220cff" }
                     ],
                     sizes: ["One Size"],
                     soldOutSizes: []
                 },
                 8: {
                     name: "Featured Product 8",
                     price: "$65",
                     images: [
                         { src: "img/sawiro/watch2.jpg", color: "default" }
                     ],
                     colors: [
                         { name: "Default", value: "default", hex: "#000" }
                     ],
                     sizes: ["One Size"],
                     soldOutSizes: []
                 }
             };

             // Open Quick View
             quickViewButtons.forEach(button => {
                 button.addEventListener('click', function(e) {
                     e.preventDefault();
                     const productId = this.getAttribute('data-product-id');
                     openQuickView(productId);
                 });
             });

             // Close Quick View
             if (closeQuickView) {
                 closeQuickView.addEventListener('click', closeQuickViewSidebar);
             }

             if (quickViewOverlay) {
                 quickViewOverlay.addEventListener('click', closeQuickViewSidebar);
             }

             // Close on escape key
             document.addEventListener('keydown', function(e) {
                 if (e.key === 'Escape' && quickViewSidebar.classList.contains('active')) {
                     closeQuickViewSidebar();
                 }
             });

             function openQuickView(productId) {
                 const product = productData[productId];
                 if (!product) return;

                 // Populate product data
                 document.getElementById('quick-view-title').textContent = product.name;
                 document.getElementById('quick-view-price').textContent = product.price;
                 
                 // Set main image
                 const mainImage = document.getElementById('quick-view-main-image');
                 mainImage.src = product.images[0].src;
                 mainImage.alt = product.name;

                 // Populate thumbnails
                 const thumbnailsContainer = document.getElementById('quick-view-thumbnails');
                 thumbnailsContainer.innerHTML = '';
                 
                 product.images.forEach((image, index) => {
                     const thumbnail = document.createElement('div');
                     thumbnail.className = thumbnail-item ${index === 0 ? 'active' : ''};
                     thumbnail.innerHTML = <img src="${image.src}" alt="${product.name} - ${image.color}" data-index="${index}">;
                     
                     thumbnail.addEventListener('click', () => {
                         // Update main image
                         mainImage.src = image.src;
                         
                         // Update active thumbnail
                         thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                         thumbnail.classList.add('active');
                     });
                     
                     thumbnailsContainer.appendChild(thumbnail);
                 });

                 // Populate colors
                 const colorSelection = document.getElementById('quick-view-color-selection');
                 colorSelection.innerHTML = '';
                 
                 product.colors.forEach((color, index) => {
                     const colorCircle = document.createElement('div');
                     colorCircle.className = quick-view-color-circle ${index === 0 ? 'active' : ''};
                     colorCircle.style.backgroundColor = color.hex;
                     colorCircle.setAttribute('data-color', color.value);
                     colorCircle.title = color.name;
                     
                     colorCircle.addEventListener('click', () => {
                         // Update active color
                         colorSelection.querySelectorAll('.quick-view-color-circle').forEach(c => c.classList.remove('active'));
                         colorCircle.classList.add('active');
                         
                         // Filter images by color
                         const selectedColor = color.value;
                         const colorImages = product.images.filter(img => img.color === selectedColor);
                         
                         if (colorImages.length > 0) {
                             // Update main image and thumbnails
                             mainImage.src = colorImages[0].src;
                             
                             // Update thumbnails
                             thumbnailsContainer.innerHTML = '';
                             colorImages.forEach((image, imgIndex) => {
                                 const thumbnail = document.createElement('div');
                                 thumbnail.className = thumbnail-item ${imgIndex === 0 ? 'active' : ''};
                                 thumbnail.innerHTML = <img src="${image.src}" alt="${product.name} - ${image.color}" data-index="${imgIndex}">;
                                 
                                 thumbnail.addEventListener('click', () => {
                                     mainImage.src = image.src;
                                     thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                                     thumbnail.classList.add('active');
                                 });
                                 
                                 thumbnailsContainer.appendChild(thumbnail);
                             });
                         }
                     });
                     
                     colorSelection.appendChild(colorCircle);
                 });

                 // Populate sizes
                 const sizeSelection = document.getElementById('quick-view-size-selection');
                 sizeSelection.innerHTML = '';
                 
                 product.sizes.forEach(size => {
                     const sizeBtn = document.createElement('button');
                     sizeBtn.className = 'quick-view-size-btn';
                     sizeBtn.textContent = size;
                     
                     if (product.soldOutSizes.includes(size)) {
                         sizeBtn.classList.add('sold-out');
                     } else {
                         sizeBtn.addEventListener('click', () => {
                             sizeSelection.querySelectorAll('.quick-view-size-btn').forEach(s => s.classList.remove('active'));
                             sizeBtn.classList.add('active');
                         });
                     }
                     
                     sizeSelection.appendChild(sizeBtn);
                 });

                 // Show sidebar
                 quickViewSidebar.classList.add('active');
                 quickViewOverlay.classList.add('active');
                 document.body.style.overflow = 'hidden'; // Prevent background scrolling
             }

             function closeQuickViewSidebar() {
                 quickViewSidebar.classList.remove('active');
                 quickViewOverlay.classList.remove('active');
                 document.body.style.overflow = ''; // Restore scrolling
             }

             // Add to bag functionality for quick view
             const addToBagQuick = document.getElementById('add-to-bag-quick');
             if (addToBagQuick) {
                 addToBagQuick.addEventListener('click', function() {
                     const selectedSize = document.querySelector('.quick-view-size-btn.active');
                     if (!selectedSize) {
                         alert('Please select a size');
                         return;
                     }
                     
                     const productName = document.getElementById('quick-view-title').textContent;
                     const selectedColor = document.querySelector('.quick-view-color-circle.active');
                     const colorName = selectedColor ? selectedColor.title : '';
                     

                     alert(Added to cart: ${productName});
                     
                     // Update cart count (you can implement this)
                     const cartCount = document.querySelector('.cart-count');
                     if (cartCount) {
                         const currentCount = parseInt(cartCount.textContent) || 0;
                         cartCount.textContent = currentCount + 1;
                     }
                 });
             }

             // Add to wishlist functionality for quick view
             const addToWishlistQuick = document.getElementById('add-to-wishlist-quick');
             if (addToWishlistQuick) {
                 addToWishlistQuick.addEventListener('click', function() {
                     const productName = document.getElementById('quick-view-title').textContent;

                     alert(Added to wishlist: ${productName});
                 });
             }

             // Heart button functionality
             const heartButtons = document.querySelectorAll('.heart-button');
             
             heartButtons.forEach(button => {
                 button.addEventListener('click', function() {
                     const icon = this.querySelector('i');
                     if (icon.classList.contains('fas')) {
                         icon.classList.remove('fas');
                         icon.classList.add('far');
                     } else {
                         icon.classList.remove('far');
                         icon.classList.add('fas');
                     }
                 });
             });

             // Color circle selection functionality
             const colorCircles = document.querySelectorAll('.color-circle');
             
             colorCircles.forEach(circle => {
                 circle.addEventListener('click', function() {
                     const productCard = this.closest('.product-card');
                     const circles = productCard.querySelectorAll('.color-circle');
                     
                     // Remove active class from all circles in this product
                     circles.forEach(c => c.classList.remove('active'));
                     
                     // Add active class to clicked circle
                     this.classList.add('active');
                 });
             });

             // Add to cart functionality
             const addToCartButtons = document.querySelectorAll('.add-to-bag');
             
             addToCartButtons.forEach(button => {
                 button.addEventListener('click', function() {
                     // Add your cart functionality here
    
                 });
             });
         });
     </script>
     
     <!-- Cart Functionality -->
     <script>
         // Load cart count on page load
         document.addEventListener('DOMContentLoaded', function() {

             loadCartCount();
         });



         function updateCartCount(count) {
             try {
                 const cartIcons = document.querySelectorAll('.fa-shopping-cart');

                 cartIcons.forEach((icon, index) => {
                     try {
                         const parent = icon.parentElement;
                         if (parent && parent.tagName) {
                             // Remove existing badge
                             const existingBadge = parent.querySelector('.cart-badge');
                             if (existingBadge) {
                                 existingBadge.remove();
                             }
                             
                             // Add new badge if count > 0
                             if (count > 0) {
                                 const badge = document.createElement('span');
                                 badge.className = 'cart-badge';
                                 badge.textContent = count;
                                 badge.style.cssText = `
                                     position: absolute;
                                     top: -8px;
                                     right: -8px;
                                     background: #e53e3e;
                                     color: white;
                                     border-radius: 50%;
                                     width: 20px;
                                     height: 20px;
                                     font-size: 12px;
                                     display: flex;
                                     align-items: center;
                                     justify-content: center;
                                     font-weight: bold;
                                 `;
                                 parent.style.position = 'relative';
                                 parent.appendChild(badge);
                             }
                         }
                     } catch (error) {
                         console.error('Error updating cart icon', index, ':', error);
                     }
                 });
             } catch (error) {
                 console.error('Error in updateCartCount:', error);
             }
         }

         function loadCartCount() {

             fetch('cart-api.php', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/x-www-form-urlencoded',
                 },
                 body: 'action=get_cart_count'
             })
             .then(response => {
                 if (!response.ok) {
                     throw new Error('HTTP error! status: ' + response.status);
                 }
                 return response.text();
             })
             .then(text => {

                 try {
                     const data = JSON.parse(text);
                     if (data.success) {
                         updateCartCount(data.cart_count);
                     } else {
                         console.error('Cart API error:', data.message);
                     }
                 } catch (e) {
                     console.error('JSON parse error:', e);
                     console.error('Response text:', text);
                 }
             })
             .catch(error => {
                 console.error('Error loading cart count:', error);
             });
         }
     </script>

     <!-- Include main JavaScript file -->
     <script src="scripts/main.js"></script>
     
 </body>
 </html>