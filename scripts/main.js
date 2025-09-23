
   var swiper = new Swiper(".mySwiper", {
    loop: true,
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });
    

// Category link toggle
const categoryLink = document.getElementById('categoryLink');
if (categoryLink) {
  categoryLink.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default anchor click behavior
    const categories = document.getElementById('categories');
    if (categories) {
      categories.style.display = categories.style.display === 'none' || categories.style.display === '' ? 'block' : 'none';
    }
  });
}

// Mobile menu toggle
const menuBtn = document.getElementById('menu-btn');
if (menuBtn) {
  menuBtn.addEventListener('click', function() {
    var menu = document.getElementById('mobile-menu');
    if (menu) {
      menu.classList.toggle('hidden');
    }
  });
}



// Color variant switching functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle color circle clicks for product variants
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('color-circle')) {
            const productCard = e.target.closest('.product-card');
            if (!productCard) return;
            
            const selectedColor = e.target.getAttribute('data-color');
            const imageSlider = productCard.querySelector('.image-slider');
            
            if (imageSlider) {
                // Remove active class from all color circles in this product
                const allColorCircles = productCard.querySelectorAll('.color-circle');
                allColorCircles.forEach(circle => circle.classList.remove('active'));
                
                // Add active class to clicked color circle
                e.target.classList.add('active');
                
                // Hide all images/videos
                const allMedia = imageSlider.querySelectorAll('img, video');
                allMedia.forEach(media => {
                    media.style.display = 'none';
                    media.classList.remove('active');
                });
                
                // Show images/videos for selected color
                const selectedMedia = imageSlider.querySelectorAll(`[data-color="${selectedColor}"]`);
                selectedMedia.forEach(media => {
                    media.style.display = 'block';
                    media.classList.add('active');
                });
                
                // If no media found for selected color, show default
                if (selectedMedia.length === 0) {
                    const defaultMedia = imageSlider.querySelectorAll('[data-color="default"]');
                    defaultMedia.forEach(media => {
                        media.style.display = 'block';
                        media.classList.add('active');
                    });
                }
            }
        }
    });
    
    // Initialize product image hover effects
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        const imageSlider = card.querySelector('.image-slider');
        if (!imageSlider) return;
        
        const images = imageSlider.querySelectorAll('img, video');
        if (images.length < 2) return;
        
        // Show first image by default
        images.forEach((img, index) => {
            if (index === 0) {
                img.style.display = 'block';
                img.classList.add('active');
            } else {
                img.style.display = 'none';
            }
        });
        
        // Hover effect to show back image
        card.addEventListener('mouseenter', function() {
            const activeImage = imageSlider.querySelector('.active');
            const nextImage = activeImage.nextElementSibling;
            
            if (nextImage && nextImage.tagName) {
                activeImage.style.display = 'none';
                activeImage.classList.remove('active');
                nextImage.style.display = 'block';
                nextImage.classList.add('active');
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const allImages = imageSlider.querySelectorAll('img, video');
            allImages.forEach((img, index) => {
                if (index === 0) {
                    img.style.display = 'block';
                    img.classList.add('active');
                } else {
                    img.style.display = 'none';
                    img.classList.remove('active');
                }
            });
        });
    });
});

// Note: Header authentication functionality is now handled by heading/header.php
// This file focuses on product-specific functionality