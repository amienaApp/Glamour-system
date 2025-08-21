<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Glamour</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="../styles/contact.css">
</head>
<body>
    
<!-- Top Navigation Bar -->
<nav class="top-nav">
    <!-- Logo Container - Left Edge -->
    <div class="logo-container">
        <div class="logo">
            <a href="#" class="logo-text">Glomour</a>
        </div>
    </div>

    <!-- Navigation Menu - Center -->
    <div class="nav-menu-container">
        <ul class="nav-menu">
            <li><a href="../index.php" class="nav-link">Home</a></li>
            <li><a href="#" class="nav-link">Categories</a></li>
            <li><a href="#" class="nav-link">order</a></li>
            <li><a href="contact.php" class="nav-link active">Contact us</a></li>
            <li><a href="#" class="nav-link">About us</a></li>
            <li><a href="#" class="nav-link">Sale</a></li>
        </ul>
    </div>

    <!-- Right Side Elements - Right Edge -->
    <div class="nav-right-container">
        <!-- Search Box -->
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search">
            <i class="fas fa-search search-icon"></i>
        </div>

        <!-- Somalia Flag -->
        <div class="flag-container">
            <img src="../img/flag.jpg" alt="Somalia Flag" class="flag" id="somalia-flag">
        </div>

        <!-- User Icon -->
        <div class="user-icon" id="user-icon">
            <i class="fas fa-user"></i>
        </div>

        <!-- Heart Icon -->
        <div class="heart-icon">
            <i class="fas fa-heart"></i>
        </div>

        <!-- Shopping Cart -->
        <div class="shopping-cart">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count">0</span>
        </div>
    </div>
</nav>

<!-- Region Selection Modal -->
<div class="modal" id="region-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Choose Region</h3>
            <button class="close-btn" id="close-region-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="region-select">Choose a region (Somalia regions):</label>
                <select id="region-select" class="form-input">
                    <option value="">Select a region</option>
                    <option value="banadir">Banadir</option>
                    <option value="bari">Bari</option>
                    <option value="bay">Bay</option>
                    <option value="galguduud">Galguduud</option>
                    <option value="gedo">Gedo</option>
                    <option value="hiran">Hiran</option>
                    <option value="jubbada-dhexe">Jubbada Dhexe</option>
                    <option value="jubbada-hoose">Jubbada Hoose</option>
                    <option value="mudug">Mudug</option>
                    <option value="nugaal">Nugaal</option>
                    <option value="sanaag">Sanaag</option>
                    <option value="shabeellaha-dhexe">Shabeellaha Dhexe</option>
                    <option value="shabeellaha-hoose">Shabeellaha Hoose</option>
                    <option value="sool">Sool</option>
                    <option value="togdheer">Togdheer</option>
                    <option value="woqooyi-galbeed">Woqooyi Galbeed</option>
                </select>
            </div>
            <div class="form-group">
                <label for="currency-select">Choose currency:</label>
                <select id="currency-select" class="form-input">
                    <option value="">Select currency</option>
                    <option value="usd">US Dollar ($)</option>
                    <option value="sos">Somali Shilling (SOS)</option>
                </select>
            </div>
            <button class="save-btn">Save Settings</button>
        </div>
    </div>
</div>

<!-- User Login Modal -->
<div class="modal" id="user-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Welcome, love!</h3>
            <button class="close-btn" id="close-user-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="tab-container">
                <div class="tab-nav">
                    <button class="tab-btn active" data-tab="signin">Sign In</button>
                    <button class="tab-btn" data-tab="signup">Create Account</button>
                </div>

                <!-- Sign In Tab -->
                <div class="tab-content active" id="signin-tab">
                    <form class="login-form">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="password-container">
                                <input type="password" id="password" class="form-input" required>
                                <span class="show-password">Show</span>
                            </div>
                        </div>
                        <button type="submit" class="signin-btn">Sign In</button>
                        <a href="#" class="forgot-password">Forgot your Password?</a>
                    </form>
                </div>

                <!-- Sign Up Tab -->
                <div class="tab-content" id="signup-tab">
                    <form class="signup-form">
                        <div class="form-group">
                            <label for="signup-name">Full Name</label>
                            <input type="text" id="signup-name" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="signup-email">Email</label>
                            <input type="email" id="signup-email" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="signup-password">Password</label>
                            <div class="password-container">
                                <input type="password" id="signup-password" class="form-input" required>
                                <span class="show-password">Show</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signup-confirm-password">Confirm Password</label>
                            <div class="password-container">
                                <input type="password" id="signup-confirm-password" class="form-input" required>
                                <span class="show-password">Show</span>
                            </div>
                        </div>
                        <button type="submit" class="signup-btn">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Button -->
<div class="chat-button">
    <i class="fas fa-comments"></i>
</div>
    
    <div class="getintouch">
       <a href="#"><h2>
          Get In Touch
        </h2></a> 

      </div>
    <section class="contact-wrapper">
      
    <div class="contact-content">
      <h2>Contact Glamor –<br>We're Here for You!</h2>
      <p>
        Have a question or feedback? Our Glamor team is always ready
        to help you with your shopping experience. Reach out and we'll get back to you
        as soon as possible<i class="fas fa-heart"  aria-hidden="true" style="color: red;"></i>.
      </p>
      <a href="#form-id" class="contact-btn">
        <img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" alt="Icon" />
        Send Us a Message
      </a>
    </div>

    <div class="contact-image">
      <img src="../img/aboutdress.avif" alt="Customer Service Image" />
    </div>
  </section>
     

  <div class="text">
       <a href="#"><h4>
          We're on a mission to bring sustainable, ethically-made products to your everyday life.
        </h4></a> 

      </div>

  <section class="section2">
   <div class="contact-container">

    <!-- Left Side -->
    <div class="contact-left">
      <div class="contact-info-grid kaladuwid">
        <div class="info-card">
          <i class="fas fa-phone"></i>
          <p><strong>Phone</strong></p>
          <p>+2529745454 <br>+25297744040</p>
        </div>
        <div class="info-card ">
          <i class="fab fa-whatsapp"></i>
          <p><strong>Whatsapp</strong></p>
          <p>+25297454545</p>
        </div>
        <div class="info-card">
          <i class="fas fa-envelope"></i>
          <p><strong>Email</strong></p>
          <p>Glamor@gmail.com</p>
        </div>
        <div class="info-card">
          <i class="fas fa-store"></i>
          <p><strong>Our Shop</strong></p>
          <p>Shaariqa Road (CF2Q+9J5)</p>
        </div>
      </div>

      <!-- Google Map -->
      <iframe
  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d10414.50731062414!2d48.4755!3d8.4084!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3d98c0f7b3ba5b7d%3A0x0!2sShaariqa%20Road%2C%20Garowe!5e0!3m2!1sen!2sso!4v1721340000000!5m2!1sen!2sso"
  width="100%"
  height="200"
  style="border:0; border-radius:12px;"
  allowfullscreen=""
  loading="lazy"
  referrerpolicy="no-referrer-when-downgrade">
</iframe>

    </div>

    <!-- Right Side -->
    <div class="contact-right">
      <h2>Get In Touch</h2>
      <p>Have a Question? We've Got Solutions!.</p>

      <form id="form-id">
        <input type="text" placeholder="Name" required />
        <input type="email" placeholder="Email" required />
        <input type="text" placeholder="Subject" />
        <textarea placeholder="Message" required></textarea>
        <button type="submit" class="send-btn">Send Now</button>
      </form>
    </div>

  </div>
  </section>

  <script src="../scripts/main.js"></script>
  
  <!-- Header JavaScript Functionality -->
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          // Scroll functionality for logo and nav-right fade
          let lastScrollTop = 0;
          const logoContainer = document.querySelector('.logo-container');
          const navRightContainer = document.querySelector('.nav-right-container');
          
          window.addEventListener('scroll', function() {
              const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
              
              // Fade out when scrolling down, fade in when scrolling up or at top
              if (scrollTop > lastScrollTop && scrollTop > 100) {
                  // Scrolling down and not at top
                  logoContainer.style.opacity = '0';
                  logoContainer.style.transform = 'translateX(-20px)';
                  navRightContainer.style.opacity = '0';
                  navRightContainer.style.transform = 'translateX(20px)';
              } else {
                  // Scrolling up or at top
                  logoContainer.style.opacity = '1';
                  logoContainer.style.transform = 'translateX(0)';
                  navRightContainer.style.opacity = '1';
                  navRightContainer.style.transform = 'translateX(0)';
              }
              
              lastScrollTop = scrollTop;
          });

          // Modal Functionality
          const somaliaFlag = document.getElementById('somalia-flag');
          const userIcon = document.getElementById('user-icon');
          const regionModal = document.getElementById('region-modal');
          const userModal = document.getElementById('user-modal');
          const closeRegionModal = document.getElementById('close-region-modal');
          const closeUserModal = document.getElementById('close-user-modal');

          // Open Region Modal
          if (somaliaFlag) {
              somaliaFlag.addEventListener('click', function() {
                  regionModal.classList.add('active');
              });
          }

          // Open User Modal
          if (userIcon) {
              userIcon.addEventListener('click', function() {
                  userModal.classList.add('active');
              });
          }

          // Close Modals
          if (closeRegionModal) {
              closeRegionModal.addEventListener('click', function() {
                  regionModal.classList.remove('active');
              });
          }

          if (closeUserModal) {
              closeUserModal.addEventListener('click', function() {
                  userModal.classList.remove('active');
              });
          }

          // Close modal when clicking outside content
          if (regionModal) {
              regionModal.addEventListener('click', function(event) {
                  if (event.target === regionModal) {
                      regionModal.classList.remove('active');
                  }
              });
          }

          if (userModal) {
              userModal.addEventListener('click', function(event) {
                  if (event.target === userModal) {
                      userModal.classList.remove('active');
                  }
              });
          }

          // Tab functionality for user modal
          const tabButtons = document.querySelectorAll('.tab-btn');
          const tabContents = document.querySelectorAll('.tab-content');

          tabButtons.forEach(button => {
              button.addEventListener('click', () => {
                  const tab = button.dataset.tab;

                  tabButtons.forEach(btn => btn.classList.remove('active'));
                  tabContents.forEach(content => content.classList.remove('active'));

                  button.classList.add('active');
                  document.getElementById(tab + '-tab').classList.add('active');
              });
          });

          // Show/Hide Password functionality
          const passwordContainers = document.querySelectorAll('.password-container');
          
          passwordContainers.forEach(container => {
              const input = container.querySelector('input');
              const showPassword = container.querySelector('.show-password');
              
              if (showPassword && input) {
                  showPassword.addEventListener('click', function() {
                      if (input.type === 'password') {
                          input.type = 'text';
                          this.textContent = 'Hide';
                      } else {
                          input.type = 'password';
                          this.textContent = 'Show';
                      }
                  });
              }
          });
      });
  </script>
  
</body>
</html>