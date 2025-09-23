<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glamor</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Include the home header CSS -->
    <link rel="stylesheet" href="heading/home-header.css?v=<?php echo time(); ?>">
     
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="bagsfolder/styles/responsive.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="footer/footer.css?v=<?php echo time(); ?>">
    
    <!-- Hide mobile elements on desktop for index page -->
    <style>
        @media (min-width: 769px) {
            .hamburger-menu,
            .mobile-filter-btn,
            .mobile-filter-overlay,
            .mobile-filter-content,
            .mobile-filter-close,
            .mobile-filter-close *,
            .mobile-filter-close:before,
            .mobile-filter-close:after,
            .mobile-filter-close::before,
            .mobile-filter-close::after,
            .mobile-nav-close,
            #mobile-nav-close,
            .mobile-header,
            .mobile-nav,
            .mobile-menu,
            .mobile-menu-toggle,
            .mobile-menu-button,
            .mobile-navigation,
            .mobile-nav-container,
            .mobile-nav-menu,
            .mobile-nav-links,
            .mobile-nav-item,
            .mobile-nav-link,
            .mobile-logo,
            .mobile-close,
            .mobile-close-btn,
            .mobile-close-button,
            .close-btn,
            .close-button,
            .menu-close,
            .nav-close,
            .mobile-menu-close,
            .hamburger-close,
            .mobile-toggle,
            .mobile-toggle-btn,
            .logo-main,
            .logo-accent,
            .logo-text,
            .close,
            .close-icon,
            .close-x,
            .x-close,
            .btn-close,
            .close-button,
            .close-btn,
            .menu-close-btn,
            .nav-close-btn,
            .header-close,
            .content-close,
            .modal-close,
            .overlay-close,
            .sidebar-close,
            .filter-close,
            .mobile-close-btn,
            .mobile-close-button,
            .mobile-menu-close,
            .hamburger-close,
            .close-icon-btn,
            .close-icon-button,
            .close-btn-icon,
            .close-button-icon,
            .close-x-btn,
            .close-x-button,
            .x-close-btn,
            .x-close-button,
            .close-icon,
            .close-symbol,
            .close-mark,
            .close-sign,
            .close-symbol-icon,
            .close-mark-icon,
            .close-sign-icon,
            .close-symbol-btn,
            .close-mark-btn,
            .close-sign-btn,
            .close-symbol-button,
            .close-mark-button,
            .close-sign-button,
            .close-icon-symbol,
            .close-icon-mark,
            .close-icon-sign,
            .close-btn-symbol,
            .close-btn-mark,
            .close-btn-sign,
            .close-button-symbol,
            .close-button-mark,
            .close-button-sign,
            .close-x-symbol,
            .close-x-mark,
            .close-x-sign,
            .x-close-symbol,
            .x-close-mark,
            .x-close-sign,
            .close-symbol-x,
            .close-mark-x,
            .close-sign-x,
            .close-symbol-icon-x,
            .close-mark-icon-x,
            .close-sign-icon-x,
            .close-symbol-btn-x,
            .close-mark-btn-x,
            .close-sign-btn-x,
            .close-symbol-button-x,
            .close-mark-button-x,
            .close-sign-button-x,
            .close-icon-symbol-x,
            .close-icon-mark-x,
            .close-icon-sign-x,
            .close-btn-symbol-x,
            .close-btn-mark-x,
            .close-btn-sign-x,
            .close-button-symbol-x,
            .close-button-mark-x,
            .close-button-sign-x,
            .close-x-symbol-x,
            .close-x-mark-x,
            .close-x-sign-x,
            .x-close-symbol-x,
            .x-close-mark-x,
            .x-close-sign-x {
                display: none !important;
            }
            
            /* Ensure desktop header is visible */
            .top-nav {
                display: flex !important;
            }
            
            .logo-container {
                display: flex !important;
            }
            
            .nav-menu-container {
                display: flex !important;
            }
            
            .nav-right-container {
                display: flex !important;
            }
        }
        
        /* Force hide mobile filter close button on desktop */
        @media (min-width: 769px) {
            .mobile-filter-close,
            .mobile-nav-close,
            #mobile-nav-close {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
                position: absolute !important;
                left: -9999px !important;
                top: -9999px !important;
                width: 0 !important;
                height: 0 !important;
                overflow: hidden !important;
            }
        }
    </style>
    
    <!-- Custom Animation Styles -->
    <style>
        /* Enhanced hover effects for cards */
        .card {
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
        }
        
        .card:hover {
            transform: translateY(-15px) scale(1.03);
            box-shadow: 
                0 20px 40px rgba(0,0,0,0.1),
                0 0 0 1px rgba(255,255,255,0.05),
                inset 0 1px 0 rgba(255,255,255,0.1);
            background: linear-gradient(145deg, #ffffff, #f0f8ff);
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .card:hover::before {
            left: 100%;
        }
        
        .card img {
            transition: all 0.4s ease;
            filter: brightness(1) contrast(1);
        }
        
        .card:hover img {
            transform: scale(1.1) rotate(2deg);
            filter: brightness(1.1) contrast(1.1) saturate(1.2);
        }
        
        /* Beautiful hover effects for media items */
        .category5 {
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            border-radius: 15px;
        }
        
        .category5:hover {
            transform: scale(1.08) rotate(1deg);
            box-shadow: 
                0 25px 50px rgba(0,0,0,0.15),
                0 0 0 1px rgba(255,255,255,0.1);
        }
        
        .category5::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                rgba(255,182,193,0.1), 
                rgba(173,216,230,0.1), 
                rgba(255,218,185,0.1));
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 1;
            pointer-events: none;
        }
        
        .category5:hover::before {
            opacity: 1;
        }
        
        .category5 video,
        .category5 img {
            transition: all 0.4s ease;
            filter: brightness(1) contrast(1);
        }
        
        .category5:hover video,
        .category5:hover img {
            transform: scale(1.15);
            filter: brightness(1.2) contrast(1.1) saturate(1.3);
        }
        
        .overlay5 {
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }
        
        .category5:hover .overlay5 {
            transform: translateY(-5px);
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        /* Stunning button hover effects */
        .btn {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 
                0 15px 35px rgba(102, 126, 234, 0.4),
                0 5px 15px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.3s ease;
        }
        
        .btn:hover::after {
            width: 300px;
            height: 300px;
        }
        
        /* Enhanced swiper slide animations */
        .swiper-slide {
            position: relative;
            overflow: hidden;
        }
        
        .swiper-slide img {
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            filter: brightness(1) contrast(1);
        }
        
        .swiper-slide:hover img {
            transform: scale(1.15) rotate(1deg);
            filter: brightness(1.1) contrast(1.1) saturate(1.2);
        }
        
        .swiper-slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                rgba(255,182,193,0.1), 
                rgba(173,216,230,0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
        
        .swiper-slide:hover::before {
            opacity: 1;
        }
        
        /* Beautiful navigation button effects */
        .swiper-button-next,
        .swiper-button-prev {
            background: rgba(255,255,255,0.9);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: rgba(102, 126, 234, 0.9);
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .swiper-button-next:after,
        .swiper-button-prev:after {
            color: #333;
            font-size: 20px;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        
        .swiper-button-next:hover:after,
        .swiper-button-prev:hover:after {
            color: white;
        }
        
        /* Glowing pagination dots */
        .swiper-pagination-bullet {
            background: rgba(102, 126, 234, 0.5);
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.2);
        }
        
        .swiper-pagination-bullet-active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            transform: scale(1.2);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
        }
        
        /* Fade in on scroll effect */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        
        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Pulse animation for special elements */
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { 
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }
            70% { 
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
            }
            100% { 
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
            }
        }
        
        /* Enhanced bounce animation for buttons */
        .bounce-hover:hover {
            animation: bounce 0.6s ease;
        }
        
        @keyframes bounce {
            0%, 20%, 60%, 100% { 
                transform: translateY(0) scale(1);
            }
            40% { 
                transform: translateY(-10px) scale(1.05);
            }
            80% { 
                transform: translateY(-5px) scale(1.02);
            }
        }
        
        /* Beautiful section title hover effect */
        .title h2 {
            position: relative;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .title h2:hover {
            transform: scale(1.05);
            text-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .title h2::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 3px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .title h2:hover::after {
            width: 100%;
        }
        
        /* Glowing effect for special elements */
        .glow-on-hover {
            transition: all 0.3s ease;
        }
        
        .glow-on-hover:hover {
            box-shadow: 
                0 0 20px rgba(102, 126, 234, 0.6),
                0 0 40px rgba(102, 126, 234, 0.4),
                0 0 60px rgba(102, 126, 234, 0.2);
      }
    </style>
    
    
</head>
<body>
    
    <!-- Include the home header -->
    <?php include 'heading/home-header.php'; ?>

    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobile-nav-overlay">
        <div class="mobile-nav-content">
            <div class="mobile-nav-header">
                <div class="mobile-nav-logo">
                    <div class="logo-main">Glamour Palace</div>
                    <div class="logo-accent">FASHION & LIFESTYLE</div>
                </div>
                <button class="mobile-nav-close" id="mobile-nav-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mobile-nav-menu">
                <ul class="mobile-nav-list">
                    <li><a href="index.php" class="mobile-nav-link">Home</a></li>
                    <li><a href="womenF/women.php" class="mobile-nav-link">Women</a></li>
                    <li><a href="menfolder/men.php" class="mobile-nav-link">Men</a></li>
                    <li><a href="kidsfolder/kids.php" class="mobile-nav-link">Kids</a></li>
                    <li><a href="beautyfolder/beauty.php" class="mobile-nav-link">Beauty</a></li>
                    <li><a href="bagsfolder/bags.php" class="mobile-nav-link">Bags</a></li>
                    <li><a href="shoess/shoes.php" class="mobile-nav-link">Shoes</a></li>
                    <li><a href="accessories/accessories.php" class="mobile-nav-link">Accessories</a></li>
                    <li><a href="perfumes/perfumes.php" class="mobile-nav-link">Perfumes</a></li>
                    <li><a href="homedecor/homedecor.php" class="mobile-nav-link">Home Decor</a></li>
                </ul>
            </div>
        </div>
    </div>
     
   



    <section class="banner-swiper-section">
        <div class="swiper banner-swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="img/banner9.webp" alt="Banner 3" class="animate__animated animate__fadeIn">
                </div>
                
                <div class="swiper-slide">
                    <img src="img/banner14.webp" alt="Banner 4" class="animate__animated animate__fadeIn">
                </div>
                
                <div class="swiper-slide">
                    <img src="img/banner5.png" alt="Banner 5" class="animate__animated animate__fadeIn">
                </div>
                
                <div class="swiper-slide">
                    <img src="img/banner6.jpg" alt="Banner 6" class="animate__animated animate__fadeIn">
                </div>
                
                <div class="swiper-slide">
                    <img src="img/banner7.jpg" alt="Banner 7" class="animate__animated animate__fadeIn">
                </div>
                
                <div class="swiper-slide">
                    <img src="img/banner8.jpg" alt="Banner 8" class="animate__animated animate__fadeIn">
                </div>
            </div>
            
            <!-- Navigation arrows -->
            <div class="swiper-button-next animate__animated animate__bounceInRight"></div>
            <div class="swiper-button-prev animate__animated animate__bounceInLeft"></div>
            
            <!-- Pagination dots -->
            <div class="swiper-pagination animate__animated animate__fadeInUp"></div>
        </div>
    </section>
    
     

         
         

         <!-- Shop by category -->
    <section  class="product container spacing">
      <div class="title animate__animated animate__fadeInDown"> 
         <h2 class="glow-on-hover">Shop by category</h2>
        <P>Explore our curated collections designed for every style and occasion</P>
    </div>
     



        <div class="pro-container container">

        <div class="card animate__animated animate__fadeInUp animate__delay-1s glow-on-hover">
                 <img src="img/menn/men.jpg" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Men's Collection</h3>
                     <P>Trending style & Timeless Classic</P>   
                                             <a href="menfolder/men.php"><button class="btn bounce-hover">Shop Now</button></a>
                 </div>
                </div>





                             <div class="card animate__animated animate__fadeInUp animate__delay-2s">
                 <img src="img/women/1.jpg" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Women's Collection</h3>
                     <P>Trending style & Timeless Classic</P>   
                                             <a href="womenF/women.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>

              

           


                               <div class="card animate__animated animate__fadeInUp animate__delay-3s">
                 <img src="img/child/1.webp" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Children's Collection</h3>
                     <P>Trending style & Timeless Classic</P>   
                                             <a href="kidsfolder/kids.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>




                                 <div class="card">
                 <img src="img/perfumes/1.jpg" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Perfumes</h3>
                     <P>Trending style & Timeless Classic</P>   
                                             <a href="perfumes/index.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>

                                                               <div class="card">
                 <img src="img/beauty/1.png" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Beauty</h3>
                     <P>Trending style & Timeless Classic</P>   
                      <a href="beautyfolder/beauty.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>


                                                               <div class="card">
                 <img src="img/cosmatics/1.jpg" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Cosmatics</h3>
                     <P>Trending style & Timeless Classic</P>   
                      <a href="beautyfolder/beauty.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>


                               <div class="card">
                 <img src="img/shoes/1.webp" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Shoes</h3>
                     <P>Trending style & Timeless Classic</P>   
                                              <a href="shoess/shoes.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>






                               <div class="card">
                 <img src="img/home-decor/1.webp" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Home Decoration</h3>
                     <P>Trending style & Timeless Classic</P>   
                                             <a href="homedecor/homedecor.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>



                               <div class="card">
                 <img src="img/bags/1.jpg" cover / center no-repeat>
                 <div class="card-content">
                    <h3>Bags</h3>
                     <P>Trending style & Timeless Classic</P>   
                                             <a href="bagsfolder/bags.php"><button class="btn">Shop Now</button></a>
                 </div>
                </div>

                                                           <div class="card">
                 <img src="img/accessories/men/watches/1.jpg" cover / center no-repeat>
                 <div class="card-content">
                    <h3> Accessories</h3>
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
             <a href="womenF/women.php?subcategory=dresses">
               <video src="./img/sawiro/dressvideo.mp4" autoplay loop muted controls> </video>
               <div class="overlay4">Wedding Dresses</div>
             </a>
           </div>
           </div>
      
      
       <!-- center -->
       <div class="gallery">
         <div class="container4">
             <div class="polaroid">
                <a href="kidsfolder/kids.php?subcategory=girls">
                   <img src="./img/sawiro/9.jpg" alt="Photo 1">
                </a>
             </div>

             <div class="polaroid">
                <a href="kidsfolder/kids.php?subcategory=girls">
                   <img src="./img/sawiro/8.jpg" alt="Photo 2">
                </a>
             </div>
             

             <div class="polaroid">
                <a href="kidsfolder/kids.php?subcategory=boys">
                   <img src="./img/sawiro/10.jpg" alt="Photo 3">
                </a>
             </div>

             <div class="polaroid">
                <a href="accessories/accessories.php?category=sunglasses">
                   <img src="./img/accessories/men/sunglasses/7.jpg" alt="Photo 4">
                </a>
             </div>

             <div class="polaroid">
                <a href="menfolder/men.php?subcategory=shirts">
                   <img src="./img/sawiro/7.jpg" alt="Photo 5">
                </a>
             </div>

             <div class="polaroid">
                <a href="womenF/women.php?subcategory=dresses">
                   <img src="./img/sawiro/21.webp" alt="Photo 6">
                </a>
             </div>

             <div class="polaroid">
               <a href="shoess/shoes.php?gender=children">
                  <img src="./img/sawiro/kidshoe.jpg" alt="Photo 7">
               </a>
             </div>

             <div class="polaroid">
               <a href="perfumes/index.php">
                  <img src="./img/sawiro/12.jpg" alt="Photo 8">
               </a>
             </div>

             <div class="polaroid">
               <a href="homedecor/homedecor.php">
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
    
       <div class="category5 animate__animated animate__zoomIn">
         <a href="womenF/women.php">
           <video src="./img/sawiro/suitvideo.mp4" autoplay loop muted controls> </video>
         </a>
       </div>
      
       <div class="category5 animate__animated animate__zoomIn animate__delay-1s">
         <a href="womenF/women.php?subcategory=dresses">
           <img src="./img/sawiro/22.webp" alt="Formal Dresses">
         </a>
       </div>

         <div class="category5">
       <a href="womenF/women.php">
         <video src="./img/sawiro/taash.mp4" autoplay loop muted controls> </video>
       </a>
       </div>
       
       <div class="category5">
         <a href="beautyfolder/beauty.php">
           <video src="./img/sawiro/makupvideo.mp4" autoplay loop muted controls ></video>
         </a>
       </div>

       <div class="category5">
         <a href="bagsfolder/bags.php">
           <img src="./img/sawiro/handbag1.jpg" alt="Handbags">
         </a>
       </div>

       <div class="category5">
         <a href="womenF/women.php">
           <video src="./img/sawiro/dressvideo2.mp4" autoplay loop muted controls ></video>
         </a>
       </div>
  

       <div class="category5">
        <a href="shoess/shoes.php">
         <video src="./img/sawiro/shoesvideo.mp4" autoplay loop muted controls ></video>
        </a>
       </div>

       <div class="category5">
         <a href="accessories/accessories.php?category=watches">
           <video src="./img/sawiro/watch.mp4" autoplay loop muted controls ></video>
         </a>
       </div>

       <div class="category5">
         <a href="accessories/accessories.php?category=jewelry">
           <img src="./img/sawiro/jwel2.jpg" alt="Jewelry">
         </a>
       </div>

       <!-- Additional Videos -->
       

       <div class="category5">
         <a href="homedecor/homedecor.php?category=living">
           <video src="./img/sawiro/home.mp4" autoplay loop muted controls ></video>
         </a>
       </div>

       <div class="category5">
         <a href="perfumes/index.php">
           <video src="./img/sawiro/perfume.mp4" autoplay loop muted controls ></video>
         </a>
       </div>

       <!-- Additional Images -->
       <div class="category5">
         <a href="menfolder/men.php?subcategory=shirts">
           <img src="./img/sawiro/24.jpg" alt="Men's Shirts">
         </a>
       </div>

       <div class="category5">
         <a href="womenF/women.php?subcategory=dresses">
           <video src="./img/sawiro/abaya.mp4" autoplay loop muted controls ></video>
         </a>
       </div>

       <div class="category5">
         <a href="bagsfolder/bags.php">
           <img src="./img/sawiro/bag.jpg" alt="Designer Handbags">
         </a>
       </div>

       <div class="category5">
         <a href="shoess/shoes.php">
           <img src="./img/sawiro/heel.jpg" alt="High Heels">
         </a>
       </div>

       <div class="category5">
         <a href="beautyfolder/beauty.php">
           <img src="./img/sawiro/skin.jpg" alt="Skincare Products">
         </a>
       </div>

       <div class="category5">
         <a href="accessories/accessories.php">
           <img src="./img/sawiro/belt.jpg" alt="Fashion Belts">
         </a>
       </div>

       <div class="category5">
         <a href="homedecor/homedecor.php">
           <img src="./img/sawiro/homedecor.jpg" alt="Home Decor Items">
         </a>
       </div>

       <div class="category5">
         <a href="kidsfolder/kids.php">
           <img src="./img/sawiro/boy.jpg" alt="Boys Clothing">
         </a>
       </div>

       <div class="category5">
         <a href="kidsfolder/kids.php">
           <img src="./img/sawiro/girl.jpg" alt="Girls Clothing">
         </a>
       </div>

       <div class="category5">
         <a href="accessories/accessories.php">
           <video src="./img/sawiro/accessories.mp4" autoplay loop muted controls ></video>
         </a>
       </div>

       <div class="category5">
         <a href="accessories/accessories.php">
           <video src="./img/sawiro/watchvideo.mp4" autoplay loop muted controls ></video>
         </a>
       </div>

       
   </div>
    </section>
  

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

  
  <!-- Include Footer -->
  <?php include 'footer/footer.php'; ?>
  
    
      


     
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
<script src="scripts/quickview-manager.js"></script>
<script src="scripts/wishlist-manager.js"></script>
<script src="scripts/sold-out-manager.js"></script>
     <script>
         document.addEventListener('DOMContentLoaded', function() {
             // Quick View buttons event listeners
             const quickViewButtons = document.querySelectorAll('.quick-view');
             
             quickViewButtons.forEach(button => {
                 button.addEventListener('click', function(e) {
                     e.preventDefault();
                     const productId = this.getAttribute('data-product-id');
                     if (window.quickviewManager) {
                         window.quickviewManager.openQuickview(productId);
                     }
                 });
             });
         });
     </script>

     <!-- Product Interaction JavaScript -->
     <script>
         document.addEventListener('DOMContentLoaded', function() {
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
     
     <!-- Include Cart Notification System -->
     <?php include 'includes/cart-notification-include.php'; ?>
     
     <!-- Cart Functionality is handled by home-header.php -->

     <!-- Bootstrap JavaScript -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     
     <!-- Animation JavaScript -->
     <script>
         // Scroll animations
         function animateOnScroll() {
             const elements = document.querySelectorAll('.animate-on-scroll');
             elements.forEach(element => {
                 const elementTop = element.getBoundingClientRect().top;
                 const elementVisible = 150;
                 
                 if (elementTop < window.innerHeight - elementVisible) {
                     element.classList.add('animated');
                 }
             });
         }
         
         // Add scroll event listener
         window.addEventListener('scroll', animateOnScroll);
         
         // Initialize animations on page load
         document.addEventListener('DOMContentLoaded', function() {
             animateOnScroll();
             
             // Add staggered animations to cards
             const cards = document.querySelectorAll('.card');
             cards.forEach((card, index) => {
                 card.style.animationDelay = `${index * 0.1}s`;
             });
             
             // Add enhanced hover effects to category items
             const categoryItems = document.querySelectorAll('.category5');
             categoryItems.forEach(item => {
                 item.addEventListener('mouseenter', function() {
                     this.style.transform = 'scale(1.08) rotate(1deg)';
                     this.style.filter = 'brightness(1.1) saturate(1.2)';
                 });
                 
                 item.addEventListener('mouseleave', function() {
                     this.style.transform = 'scale(1) rotate(0deg)';
                     this.style.filter = 'brightness(1) saturate(1)';
                 });
             });
             
             // Add beautiful hover effects to cards
             const cards = document.querySelectorAll('.card');
             cards.forEach(card => {
                 card.addEventListener('mouseenter', function() {
                     this.style.transform = 'translateY(-15px) scale(1.03)';
                     this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.1)';
                 });
                 
                 card.addEventListener('mouseleave', function() {
                     this.style.transform = 'translateY(0) scale(1)';
                     this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
                 });
             });
             
             // Add floating animation to buttons
             const buttons = document.querySelectorAll('.btn');
             buttons.forEach(button => {
                 button.addEventListener('mouseenter', function() {
                     this.style.transform = 'translateY(-3px) scale(1.05)';
                     this.style.boxShadow = '0 15px 35px rgba(102, 126, 234, 0.4), 0 5px 15px rgba(0,0,0,0.1)';
                 });
                 
                 button.addEventListener('mouseleave', function() {
                     this.style.transform = 'translateY(0) scale(1)';
                     this.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.3)';
                 });
             });
             
             // Add click animations to buttons
             buttons.forEach(button => {
                 button.addEventListener('click', function(e) {
                     // Create ripple effect
                     const ripple = document.createElement('span');
                     const rect = this.getBoundingClientRect();
                     const size = Math.max(rect.width, rect.height);
                     const x = e.clientX - rect.left - size / 2;
                     const y = e.clientY - rect.top - size / 2;
                     
                     ripple.style.width = ripple.style.height = size + 'px';
                     ripple.style.left = x + 'px';
                     ripple.style.top = y + 'px';
                     ripple.classList.add('ripple');
                     
                     this.appendChild(ripple);
                     
                     setTimeout(() => {
                         ripple.remove();
                     }, 600);
                 });
             });
             
             // Add beautiful text hover effects
             const headings = document.querySelectorAll('h2, h3');
             headings.forEach(heading => {
                 heading.addEventListener('mouseenter', function() {
                     this.style.textShadow = '0 5px 15px rgba(102, 126, 234, 0.3)';
                     this.style.transform = 'scale(1.02)';
                 });
                 
                 heading.addEventListener('mouseleave', function() {
                     this.style.textShadow = 'none';
                     this.style.transform = 'scale(1)';
                 });
             });
             
             // Add floating effect to images
             const images = document.querySelectorAll('img');
             images.forEach(img => {
                 img.addEventListener('mouseenter', function() {
                     this.style.transform = 'scale(1.05) rotate(1deg)';
                     this.style.filter = 'brightness(1.1) contrast(1.1) saturate(1.2)';
                 });
                 
                 img.addEventListener('mouseleave', function() {
                     this.style.transform = 'scale(1) rotate(0deg)';
                     this.style.filter = 'brightness(1) contrast(1) saturate(1)';
                 });
             });
         });
         
         // Add CSS for ripple effect
         const style = document.createElement('style');
         style.textContent = `
             .btn {
                 position: relative;
                 overflow: hidden;
             }
             
             .ripple {
                 position: absolute;
                 border-radius: 50%;
                 background: rgba(255, 255, 255, 0.6);
                 transform: scale(0);
                 animation: ripple 0.6s linear;
                 pointer-events: none;
             }
             
             @keyframes ripple {
                 to {
                     transform: scale(4);
                     opacity: 0;
                 }
             }
         `;
         document.head.appendChild(style);
     </script>
     
 </body>
 </html>
