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
                <a href="womenF/women.php?subcategory=dresses">
                   <img src="./img/sawiro/9.jpg" alt="Photo 1">
                </a>
             </div>

             <div class="polaroid">
                <a href="womenF/women.php?subcategory=dresses">
                   <img src="./img/sawiro/8.jpg" alt="Photo 2">
                </a>
             </div>
             

             <div class="polaroid">
                <a href="womenF/women.php?subcategory=dresses">
                   <img src="./img/sawiro/10.jpg" alt="Photo 3">
                </a>
             </div>

             <div class="polaroid">
                <a href="accessories/accessories.php?category=sunglasses">
                   <img src="./img/accessories/men/sunglasses/7.jpg" alt="Photo 4">
                </a>
             </div>

             <div class="polaroid">
                <a href="womenF/women.php?subcategory=dresses">
                   <img src="./img/sawiro/7.jpg" alt="Photo 5">
                </a>
             </div>

             <div class="polaroid">
                <a href="womenF/women.php?subcategory=dresses">
                   <img src="./img/sawiro/21.webp" alt="Photo 6">
                </a>
             </div>

             <div class="polaroid">
               <a href="shoess/men.php?category=children">
                  <img src="./img/sawiro/kidshoe.jpg" alt="Photo 7">
               </a>
             </div>

             <div class="polaroid">
               <a href="womenF/women.php?subcategory=dresses">
                  <img src="./img/sawiro/12.jpg" alt="Photo 8">
               </a>
             </div>

             <div class="polaroid">
               <a href="womenF/women.php?subcategory=dresses">
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
         <a href="menfolder/men.php?category=suits">
           <video src="./img/sawiro/suitvideo.mp4" autoplay loop muted controls> </video>
           <div class="overlay5">suit Dresses</div>
         </a>
       </div>
      
       <div class="category5 animate__animated animate__zoomIn animate__delay-1s">
         <a href="womenF/women.php?subcategory=dresses">
           <img src="./img/sawiro/22.webp" alt="Formal Dresses">
           <div class="overlay5">Formal Dresses</div>
         </a>
       </div>

         <div class="category5">
       <a href="womenF/women.php?subcategory=dresses">
         <video src="./img/sawiro/taash.mp4" autoplay loop muted controls> </video>
         <div class="overlay5">Bride Dresses</div>
       </a>
       </div>
       
       <div class="category5">
         <a href="beautyfolder/beauty.php">
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
         <a href="womenF/women.php?subcategory=dresses">
           <video src="./img/sawiro/dressvideo2.mp4" autoplay loop muted controls ></video>
           <div class="overlay5">Formal Dresses</div>
         </a>
       </div>
  

       <div class="category5">
        <a href="shoess/shoes.php">
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

       <!-- Additional Videos -->
       <div class="category5">
         <a href="kidsfolder/kids.php">
           <video src="./img/sawiro/kidsvideo.mp4" autoplay loop muted controls ></video>
           <div class="overlay5">Kids Collection</div>
         </a>
       </div>

       <div class="category5">
         <a href="homedecor/homedecor.php">
           <video src="./img/sawiro/homedecorvideo.mp4" autoplay loop muted controls ></video>
           <div class="overlay5">Home Decor</div>
         </a>
       </div>

       <div class="category5">
         <a href="perfumes/index.php">
           <video src="./img/sawiro/perfumevideo.mp4" autoplay loop muted controls ></video>
           <div class="overlay5">Perfumes</div>
         </a>
       </div>

       <!-- Additional Images -->
       <div class="category5">
         <a href="menfolder/men.php?subcategory=shirts">
           <img src="./img/sawiro/menshirt1.jpg" alt="Men's Shirts">
           <div class="overlay5">Men's Shirts</div>
         </a>
       </div>

       <div class="category5">
         <a href="womenF/women.php?subcategory=tops">
           <img src="./img/sawiro/womentop1.jpg" alt="Women's Tops">
           <div class="overlay5">Women's Tops</div>
         </a>
       </div>

       <div class="category5">
         <a href="bagsfolder/bags.php?category=handbags">
           <img src="./img/sawiro/handbag3.jpg" alt="Designer Handbags">
           <div class="overlay5">Designer Handbags</div>
         </a>
       </div>

       <div class="category5">
         <a href="shoess/shoes.php?category=heels">
           <img src="./img/sawiro/heels1.jpg" alt="High Heels">
           <div class="overlay5">High Heels</div>
         </a>
       </div>

       <div class="category5">
         <a href="beautyfolder/beauty.php?subcategory=skincare">
           <img src="./img/sawiro/skincare1.jpg" alt="Skincare Products">
           <div class="overlay5">Skincare</div>
         </a>
       </div>

       <div class="category5">
         <a href="accessories/accessories.php?category=belts">
           <img src="./img/sawiro/belt1.jpg" alt="Fashion Belts">
           <div class="overlay5">Fashion Belts</div>
         </a>
       </div>

       <div class="category5">
         <a href="homedecor/homedecor.php?category=decor">
           <img src="./img/sawiro/homedecor1.jpg" alt="Home Decor Items">
           <div class="overlay5">Home Decor</div>
         </a>
       </div>

       <div class="category5">
         <a href="kidsfolder/kids.php?subcategory=boys">
           <img src="./img/sawiro/kidsboy1.jpg" alt="Boys Clothing">
           <div class="overlay5">Boys Collection</div>
         </a>
       </div>

       <div class="category5">
         <a href="kidsfolder/kids.php?subcategory=girls">
           <img src="./img/sawiro/kidsgirl1.jpg" alt="Girls Clothing">
           <div class="overlay5">Girls Collection</div>
         </a>
       </div>

       <div class="category5">
         <a href="womenF/women.php?subcategory=accessories">
           <video src="./img/sawiro/womenaccessories.mp4" autoplay loop muted controls ></video>
           <div class="overlay5">Women's Accessories</div>
         </a>
       </div>

       <div class="category5">
         <a href="menfolder/men.php?subcategory=accessories">
           <video src="./img/sawiro/menaccessories.mp4" autoplay loop muted controls ></video>
           <div class="overlay5">Men's Accessories</div>
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
                <select id="sort-select" class="sort-select" onchange="updateSort(this.value)">
                    <option value="newest" selected>Newest</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="popular">Most Popular</option>
                </select>
            </div>
        </div>
    </div>
    

    <div class="product-grid" data-category="shirts">
       
        <div class="product-card" data-product-id="1" data-product-stock="5" data-product-available="true">
            <div class="product-image">
                <div class="image-slider">
                    <img src="img/sawiro/23.jpg" alt="Product 23 - Front" class="active" data-color="default">
                </div>
                <button class="heart-button" data-product-id="1">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                    <button class="quick-view" data-product-id="1">Quick View</button>
                    <button class="add-to-bag" data-product-id="1">Add To Bag</button>
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                    <span class="color-circle active" style="background-color: #96e0f3ff;" title="Default" data-color="default"></span>
                </div>
                <h3 class="product-name">Featured Product 1</h3>
                <div class="product-price">$25</div>
                <div class="product-availability" style="display: none;">In Stock</div>
            </div>
        </div>

        <!-- Product 2 - SOLD OUT -->
        <div class="product-card sold-out" data-product-id="2" data-product-stock="0" data-product-available="false">
            <div class="product-image">
                <div class="image-slider">
                    <img src="img/sawiro/24.jpg" alt="Product 24 - Front" class="active" data-color="default">
                </div>
                <button class="heart-button" data-product-id="2">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                    <button class="quick-view" data-product-id="2">Quick View</button>
                    <button class="add-to-bag sold-out-btn" disabled>Sold Out</button>
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                    <span class="color-circle active" style="background-color:#060606ff;" title="Default" data-color="default"></span>
                </div>
                <h3 class="product-name">Featured Product 2</h3>
                <div class="product-price">$30</div>
                <div class="product-availability sold-out-text">SOLD OUT</div>
            </div>
        </div>

        <!-- Product 3 -->
        <div class="product-card" data-product-id="3" data-product-stock="3" data-product-available="true">
            <div class="product-image">
                <div class="image-slider">
                    <img src="img/sawiro/20.webp" alt="Product 20 - Front" class="active" data-color="default">
                </div>
                <button class="heart-button" data-product-id="3">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                    <button class="quick-view" data-product-id="3">Quick View</button>
                    <button class="add-to-bag" data-product-id="3">Add To Bag</button>
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                    <span class="color-circle active" style="background-color: #000;" title="Default" data-color="default"></span>
                </div>
                <h3 class="product-name">Featured Product 3</h3>
                <div class="product-price">$28</div>
                <div class="product-availability" style="display: none;">In Stock</div>
            </div>
        </div>

        <!-- Product 4 -->
        <div class="product-card" data-product-id="4" data-product-stock="8" data-product-available="true">
            <div class="product-image">
                <div class="image-slider">
                    <img src="img/sawiro/barca.jpg" alt="Barca Product - Front" class="active" data-color="default">
                </div>
                <button class="heart-button" data-product-id="4">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                    <button class="quick-view" data-product-id="4">Quick View</button>
                    <button class="add-to-bag" data-product-id="4">Add To Bag</button>
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                    <span class="color-circle active" style="background-color: #001f3f;" title="Default" data-color="default"></span>
                </div>
                <h3 class="product-name">Featured Product 4</h3>
                <div class="product-price">$35</div>
                <div class="product-availability" style="display: none;">In Stock</div>
            </div>
        </div>

        <!-- Product 5 - SOLD OUT -->
        <div class="product-card sold-out" data-product-id="5" data-product-stock="0" data-product-available="false">
            <div class="product-image">
                <div class="image-slider">
                    <img src="img/sawiro/glas2.jpg" alt="Designer Glasses - Front" class="active" data-color="maroon">
                    <img src="img/sawiro/glas1.jpg" alt="Designer Glasses - Front" data-color="black">
                </div>
                <button class="heart-button" data-product-id="5">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                    <button class="quick-view" data-product-id="5">Quick View</button>
                    <button class="add-to-bag sold-out-btn" disabled>Sold Out</button>
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                    <span class="color-circle active" style="background-color: #8B0000;" title="Maroon" data-color="maroon"></span>
                    <span class="color-circle" style="background-color: #000000;" title="Black" data-color="black"></span>
                </div>
                <h3 class="product-name">Designer Glasses</h3>
                <div class="product-price">$45</div>
                <div class="product-availability sold-out-text">SOLD OUT</div>
            </div>
        </div>

        <!-- Product 6 -->
        <div class="product-card" data-product-id="6" data-product-stock="2" data-product-available="true">
            <div class="product-image">
                <div class="image-slider">
                    <img src="img/sawiro/handbag2.jpg" alt="Handbag Product - Front" class="active" data-color="default">
                </div>
                <button class="heart-button" data-product-id="6">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                    <button class="quick-view" data-product-id="6">Quick View</button>
                    <button class="add-to-bag" data-product-id="6">Add To Bag</button>
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                    <span class="color-circle active" style="background-color: #ffffffff;" title="Default" data-color="default"></span>
                </div>
                <h3 class="product-name">Featured Product 6</h3>
                <div class="product-price">$55</div>
                <div class="product-availability" style="display: none;">In Stock</div>
            </div>
        </div>

        <!-- Product 7 -->
        <div class="product-card" data-product-id="7" data-product-stock="10" data-product-available="true">
            <div class="product-image">
                <div class="image-slider">
                    <img src="img/sawiro/jwel2.jpg" alt="Jewelry Product - Front" class="active" data-color="default">
                </div>
                <button class="heart-button" data-product-id="7">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                    <button class="quick-view" data-product-id="7">Quick View</button>
                    <button class="add-to-bag" data-product-id="7">Add To Bag</button>
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                    <span class="color-circle active" style="background-color: #14220cff" title="Default" data-color="default"></span>
                </div>
                <h3 class="product-name">Featured Product 7</h3>
                <div class="product-price">$38</div>
                <div class="product-availability" style="display: none;">In Stock</div>
            </div>
        </div>

            

        <!-- Product 8 -->
        <div class="product-card" data-product-id="8" data-product-stock="1" data-product-available="true">
            <div class="product-image">
                <div class="image-slider">
                    <img src="img/sawiro/watch2.jpg" alt="Watch Product - Front" class="active" data-color="default">
                </div>
                <button class="heart-button" data-product-id="8">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                    <button class="quick-view" data-product-id="8">Quick View</button>
                    <button class="add-to-bag" data-product-id="8">Add To Bag</button>
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                    <span class="color-circle active" style="background-color: #000;" title="Default" data-color="default"></span>
                </div>
                <h3 class="product-name">Featured Product 8</h3>
                <div class="product-price">$65</div>
                <div class="product-availability" style="display: none;">In Stock</div>
            </div>
        </div>

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
     
     <!-- Simple Sorting Function -->
     <script>
     function updateSort(sortValue) {
         const params = new URLSearchParams(window.location.search);
         params.set('sort', sortValue);
         
         const newUrl = window.location.pathname + '?' + params.toString();
         window.history.pushState({}, '', newUrl);
         window.location.reload();
     }
     </script>
     
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
                     console.log('Add to cart clicked for product:', this.getAttribute('data-product-id'));
                 });
             });
         });
     </script>
     
     <!-- Include Cart Notification System -->
     <?php include 'includes/cart-notification-include.php'; ?>
     
     <!-- Cart Functionality -->
     <script>
         // Load cart count on page load
         document.addEventListener('DOMContentLoaded', function() {
             // Use the unified cart notification manager if available
             if (window.cartNotificationManager) {
                 window.cartNotificationManager.loadCartCount();
             } else {
                 loadCartCount();
             }
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
                                     box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                                     animation: cartCountBounce 0.3s ease-in-out;
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
