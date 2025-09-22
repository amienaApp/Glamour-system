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
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
            opacity: 0.8;
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

            .story-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .story-text {
                padding-right: 0;
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
                    <span class="stat-number">10+</span>
                    <span class="stat-label">Years of Excellence</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">50K+</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">1000+</span>
                    <span class="stat-label">Products</span>
                </div>
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
                    <p>Founded in 2014, Glamour began as a dream to create a fashion destination that celebrates individuality and empowers people to express their unique style. What started as a small boutique has grown into a comprehensive fashion empire, offering everything from trendy accessories to timeless classics.</p>
                    <p>Our commitment to quality, innovation, and customer satisfaction has been the cornerstone of our success. We believe that fashion is not just about clothing—it's about confidence, self-expression, and feeling beautiful in your own skin.</p>
                </div>
                <div class="story-image">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Glamour Store">
                </div>
            </div>

            <div class="story-grid">
                <div class="story-image">
                    <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Fashion Collection">
                </div>
                <div class="story-text">
                    <h3>Innovation Meets Tradition</h3>
                    <p>At Glamour, we blend cutting-edge fashion trends with timeless elegance. Our curated collections feature the latest styles from international designers alongside carefully selected pieces that stand the test of time.</p>
                    <p>We've embraced technology to enhance the shopping experience, offering seamless online shopping, virtual try-ons, and personalized recommendations. Yet, we never forget the human touch that makes fashion personal and meaningful.</p>
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

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2 class="section-title">Meet Our Team</h2>
            <p class="section-subtitle">The passionate individuals behind Glamour who make your fashion dreams come true</p>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Amina abdiqafar</h3>
                        <p class="member-role">Founder & CEO</p>
                        <p class="member-bio">With over 15 years in the fashion industry, Sarah's vision and leadership have been instrumental in making Glamour the success it is today.</p>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-image">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Fatima mohamud</h3>
                        <p class="member-role">Creative Director</p>
                        <p class="member-bio">fatima's keen eye for trends and passion for design ensures that every collection we offer is both stylish and accessible.</p>
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

