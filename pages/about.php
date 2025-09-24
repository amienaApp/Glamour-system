<?php
// Start session before any HTML output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Glamour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../heading/home-header.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #f8fbff 0%, #e6f3ff 100%);
        }

        .about-hero {
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
            color: white;
            padding: 120px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-gallery {
            position: absolute;
            bottom: -50px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 20px;
            z-index: 3;
        }

        .hero-image {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }

        .hero-image:hover {
            transform: translateY(-10px) scale(1.05);
        }

        .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            animation: fadeInUp 1s ease-out;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            font-weight: 300;
            margin-bottom: 30px;
            opacity: 0.9;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 50px;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .about-content {
            padding: 80px 0;
            background: white;
        }

        .gallery-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #f8fbff 0%, #e6f3ff 100%);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 60px;
        }

        .gallery-item {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover {
            transform: translateY(-10px);
        }

        .gallery-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 30px 20px 20px;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover .gallery-overlay {
            transform: translateY(0);
        }

        .gallery-overlay h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .gallery-overlay p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .video-section {
            padding: 80px 0;
            background: white;
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .video-item {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .video-item:hover {
            transform: translateY(-10px);
        }

        .video-item video {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .video-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 30px 20px 20px;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .video-item:hover .video-overlay {
            transform: translateY(0);
        }

        .video-overlay h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .video-overlay p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 60px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .story-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            margin-bottom: 80px;
        }

        .story-text {
            padding-right: 40px;
        }

        .story-text h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .story-text p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            margin-bottom: 20px;
        }

        .story-image {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .story-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .story-image:hover img {
            transform: scale(1.05);
        }

        .values-section {
            background: linear-gradient(135deg, #f8fbff 0%, #e6f3ff 100%);
            padding: 80px 0;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .value-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .value-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
        }

        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .value-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            color: white;
        }

        .value-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .value-description {
            color: #666;
            line-height: 1.6;
        }

        .team-section {
            padding: 80px 0;
            background: white;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .team-member {
            text-align: center;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .team-member:hover {
            transform: translateY(-10px);
        }

        .member-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
            position: relative;
        }

        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .team-member:hover .member-image img {
            transform: scale(1.1);
        }

        .member-info {
            padding: 30px 20px;
        }

        .member-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .member-role {
            color: #0066cc;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .member-bio {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .cta-section {
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .cta-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .cta-text {
            font-size: 1.1rem;
            margin-bottom: 40px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: white;
            color: #0066cc;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255,255,255,0.3);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: white;
            color: #0066cc;
            transform: translateY(-2px);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-stats {
                flex-direction: column;
                gap: 30px;
            }

            .hero-gallery {
                flex-direction: column;
                gap: 15px;
                bottom: -100px;
            }

            .hero-image {
                width: 80px;
                height: 80px;
            }

            .story-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .story-text {
                padding-right: 0;
            }

            .gallery-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .video-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .values-grid {
                grid-template-columns: 1fr;
            }

            .team-grid {
                grid-template-columns: 1fr;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <?php include '../heading/home-header.php'; ?>
    
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="hero-content">
            <h1 class="hero-title">Our Story</h1>
            <p class="hero-subtitle">Discover the passion behind Glamour - where fashion meets elegance and style meets sophistication</p>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number">1</span>
                    <span class="stat-label">Year of Excellence</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">200+</span>
                    <span class="stat-label">Products</span>
                </div>
            </div>
        </div>
        
        <!-- Hero Image Gallery -->
        <div class="hero-gallery">
            <div class="hero-image">
                <img src="../img/sawiro/21.webp" alt="Fashion Collection">
            </div>
            <div class="hero-image">
                <img src="../img/sawiro/22.webp" alt="Elegant Style">
            </div>
            <div class="hero-image">
                <img src="../img/sawiro/24.jpg" alt="Modern Fashion">
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="about-content">
        <div class="container">
            <h2 class="section-title">The Glamour Journey</h2>
            <p class="section-subtitle">From a small boutique to a leading fashion destination, our journey has been nothing short of extraordinary</p>
            
            <div class="story-grid">
                <div class="story-text">
                    <h3>A Vision of Elegance</h3>
                    <p>Founded in 2023, Glamour began as a dream to create a fashion destination that celebrates individuality and empowers people to express their unique style. In just one year, we've grown from a small boutique into a comprehensive fashion destination, offering everything from trendy accessories to timeless classics.</p>
                    <p>Our commitment to quality, innovation, and customer satisfaction has been the cornerstone of our rapid growth. We believe that fashion is not just about clothing—it's about confidence, self-expression, and feeling beautiful in your own skin.</p>
                </div>
                <div class="story-image">
                    <img src="../img/sawiro/7.jpg" alt="Glamour Store">
                </div>
            </div>

            <div class="story-grid">
                <div class="story-image">
                    <img src="../img/sawiro/8.jpg" alt="Fashion Collection">
                </div>
                <div class="story-text">
                    <h3>Innovation Meets Tradition</h3>
                    <p>At Glamour, we blend cutting-edge fashion trends with timeless elegance. Our curated collections feature the latest styles from international designers alongside carefully selected pieces that stand the test of time.</p>
                    <p>We've embraced technology to enhance the shopping experience, offering seamless online shopping, virtual try-ons, and personalized recommendations. Yet, we never forget the human touch that makes fashion personal and meaningful.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Showcase Gallery -->
    <section class="gallery-section">
        <div class="container">
            <h2 class="section-title">Our Beautiful Collections</h2>
            <p class="section-subtitle">Discover the stunning pieces that make Glamour your go-to fashion destination</p>
            
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="../img/sawiro/9.jpg" alt="Elegant Dresses">
                    <div class="gallery-overlay">
                        <h3>Elegant Dresses</h3>
                        <p>Timeless beauty for every occasion</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="../img/sawiro/10.jpg" alt="Men's Fashion">
                    <div class="gallery-overlay">
                        <h3>Men's Fashion</h3>
                        <p>Sophisticated style for the modern man</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="../img/sawiro/12.jpg" alt="Accessories">
                    <div class="gallery-overlay">
                        <h3>Luxury Accessories</h3>
                        <p>Perfect finishing touches</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="../img/sawiro/13.jpg" alt="Home Decor">
                    <div class="gallery-overlay">
                        <h3>Home Decor</h3>
                        <p>Beautiful spaces, beautiful life</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="../img/sawiro/20.webp" alt="Beauty Products">
                    <div class="gallery-overlay">
                        <h3>Beauty Products</h3>
                        <p>Enhance your natural beauty</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="../img/sawiro/barca.jpg" alt="Barca Collection">
                    <div class="gallery-overlay">
                        <h3>Barca Collection</h3>
                        <p>Exclusive sports-inspired fashion</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values Section -->
    <section class="values-section">
        <div class="container">
            <h2 class="section-title">Our Core Values</h2>
            <p class="section-subtitle">These principles guide everything we do and shape the experience we create for our customers</p>
            
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="value-title">Passion for Fashion</h3>
                    <p class="value-description">We live and breathe fashion. Every piece we select is chosen with love and care, ensuring it meets our high standards of quality and style.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="value-title">Excellence in Service</h3>
                    <p class="value-description">We believe in going above and beyond for our customers. From personalized styling advice to seamless shopping experiences, your satisfaction is our priority.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3 class="value-title">Sustainable Fashion</h3>
                    <p class="value-description">We're committed to promoting sustainable fashion practices and working with brands that share our values of environmental responsibility.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="value-title">Community First</h3>
                    <p class="value-description">We're more than just a store—we're a community of fashion enthusiasts who support and inspire each other to look and feel their best.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3 class="value-title">Innovation</h3>
                    <p class="value-description">We constantly evolve and adapt to bring you the latest trends and technologies, ensuring you always have access to the best fashion has to offer.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="value-title">Integrity</h3>
                    <p class="value-description">We conduct our business with honesty, transparency, and respect. Your trust is our most valuable asset, and we work hard to maintain it.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Showcase Section -->
    <section class="video-section">
        <div class="container">
            <h2 class="section-title">See Glamour in Action</h2>
            <p class="section-subtitle">Watch our beautiful collections come to life</p>
            
            <div class="video-grid">
                <div class="video-item">
                    <video autoplay loop muted>
                        <source src="../img/sawiro/dressvideo.mp4" type="video/mp4">
                    </video>
                    <div class="video-overlay">
                        <h3>Elegant Dresses</h3>
                        <p>Timeless beauty in motion</p>
                    </div>
                </div>
                <div class="video-item">
                    <video autoplay loop muted>
                        <source src="../img/sawiro/manvideo.mp4" type="video/mp4">
                    </video>
                    <div class="video-overlay">
                        <h3>Men's Fashion</h3>
                        <p>Sophisticated style</p>
                    </div>
                </div>
                <div class="video-item">
                    <video autoplay loop muted>
                        <source src="../img/sawiro/makupvideo.mp4" type="video/mp4">
                    </video>
                    <div class="video-overlay">
                        <h3>Beauty Products</h3>
                        <p>Enhance your natural glow</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2 class="section-title">Meet Our Team</h2>
            <p class="section-subtitle">The passionate individuals behind Glamour who make your fashion dreams come true</p>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image">
                        <img src="../img/sawiro/girl.jpg" alt="Amina abdiqafar">
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Amina abdiqafar</h3>
                        <p class="member-role">Founder & CEO</p>
                        <p class="member-bio">With a passion for fashion and entrepreneurial spirit, Amina's vision and leadership have been instrumental in making Glamour the success it is today.</p>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-image">
                        <img src="../img/sawiro/boy.jpg" alt="Fatima mohamud">
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Fatima mohamud</h3>
                        <p class="member-role">Creative Director</p>
                        <p class="member-bio">Fatima's keen eye for trends and passion for design ensures that every collection we offer is both stylish and accessible.</p>
                    </div>
                </div>
                
                
                
                
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Join the Glamour Family</h2>
            <p class="cta-text">Ready to discover your perfect style? Explore our collections and become part of our growing community of fashion enthusiasts.</p>
            <div class="cta-buttons">
                <a href="../index.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i>
                    Shop Now
                </a>
                <a href="contact.php" class="btn btn-secondary">
                    <i class="fas fa-envelope"></i>
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    <script>
        // Add scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards and sections
        document.querySelectorAll('.value-card, .team-member, .story-grid').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });

        // Animate stats on scroll
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumbers = entry.target.querySelectorAll('.stat-number');
                    statNumbers.forEach(stat => {
                        const target = parseInt(stat.textContent);
                        let current = 0;
                        const increment = target / 50;
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= target) {
                                current = target;
                                clearInterval(timer);
                            }
                            stat.textContent = Math.floor(current) + (stat.textContent.includes('+') ? '+' : '');
                        }, 30);
                    });
                    statsObserver.unobserve(entry.target);
                }
            });
        });

        const statsSection = document.querySelector('.hero-stats');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }
    </script>
</body>
</html>