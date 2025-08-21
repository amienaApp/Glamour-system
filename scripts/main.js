
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