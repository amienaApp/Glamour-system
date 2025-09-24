<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Glamour</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../heading/home-header.css">
  <link rel="stylesheet" href="../styles/contact.css">
</head>
<body>
    
    <?php include '../heading/home-header.php'; ?>


    
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
        <div class="info-card clickable-card" onclick="makePhoneCall('+252907166125')">
          <i class="fas fa-phone"></i>
          <p><strong>Phone</strong></p>
          <p>+252907166125</p>
        </div>
        <div class="info-card clickable-card" onclick="openWhatsApp('+252907166125')">
          <i class="fab fa-whatsapp"></i>
          <p><strong>Whatsapp</strong></p>
          <p>+252907166125</p>
        </div>
        <div class="info-card clickable-card" onclick="openEmail('glamorpalaca@gmail.com')">
          <i class="fas fa-envelope"></i>
          <p><strong>Email</strong></p>
          <p>glamorpalaca@gmail.com</p>
        </div>
        <div class="info-card clickable-card" onclick="openGoogleMaps('Shaariqa Road, Garowe, Somalia')">
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

      <form id="form-id" action="../contact-handler.php" method="POST">
        <input type="text" name="name" placeholder="Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <textarea name="message" placeholder="Message" required></textarea>
        <button type="submit" class="send-btn">Send Now</button>
      </form>
      
      <!-- Success/Error Message Display -->
      <div id="messageDiv" style="margin-top: 15px; padding: 10px; border-radius: 5px; display: none;"></div>
    </div>

  </div>
  </section>

  <script src="../scripts/main.js"></script>
  
  <script>
    // Handle contact form submission
    document.getElementById('form-id').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const messageDiv = document.getElementById('messageDiv');
      
      // Show loading state
      messageDiv.style.display = 'block';
      messageDiv.style.backgroundColor = '#e3f2fd';
      messageDiv.style.color = '#1976d2';
      messageDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending your message...';
      
      fetch('../contact-handler.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          messageDiv.style.backgroundColor = '#e8f5e8';
          messageDiv.style.color = '#2e7d32';
          messageDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
          this.reset(); // Clear the form
        } else {
          messageDiv.style.backgroundColor = '#ffebee';
          messageDiv.style.color = '#c62828';
          messageDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
        }
      })
      .catch(error => {
        messageDiv.style.backgroundColor = '#ffebee';
        messageDiv.style.color = '#c62828';
        messageDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> An error occurred. Please try again.';
        console.error('Error:', error);
      });
    });
  </script>
  
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
  
  <!-- Contact Card Functionality -->
  <script>
    // Function to make phone call
    function makePhoneCall(phoneNumber) {
      // Remove any spaces or special characters except +
      const cleanNumber = phoneNumber.replace(/[^\d+]/g, '');
      window.location.href = `tel:${cleanNumber}`;
    }
    
    // Function to open WhatsApp
    function openWhatsApp(phoneNumber) {
      // Remove any spaces or special characters except +
      const cleanNumber = phoneNumber.replace(/[^\d+]/g, '');
      const whatsappUrl = `https://wa.me/${cleanNumber}`;
      window.open(whatsappUrl, '_blank');
    }
    
     // Function to open email client
     function openEmail(emailAddress) {
       console.log('Opening email for:', emailAddress); // Debug log
       
       // Force Gmail web to open (like WhatsApp)
       const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${emailAddress}`;
       console.log('Gmail URL:', gmailUrl); // Debug log
       window.open(gmailUrl, '_blank');
     }
    
    // Function to open Google Maps
    function openGoogleMaps(address) {
      const encodedAddress = encodeURIComponent(address);
      const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodedAddress}`;
      window.open(mapsUrl, '_blank');
    }
    
    // Add hover effects for clickable cards
    document.addEventListener('DOMContentLoaded', function() {
      const clickableCards = document.querySelectorAll('.clickable-card');
      
      clickableCards.forEach(card => {
        // Add cursor pointer
        card.style.cursor = 'pointer';
        
        // Add hover effect
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-5px)';
          this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
          this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
          this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
        });
        
        // Add click animation
        card.addEventListener('click', function() {
          this.style.transform = 'scale(0.95)';
          setTimeout(() => {
            this.style.transform = 'translateY(-5px)';
          }, 150);
        });
      });
    });
  </script>
  
</body>
</html>