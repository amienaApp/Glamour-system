
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
    

document.getElementById('categoryLink').addEventListener('click', function(event) {
  event.preventDefault(); // Prevent default anchor click behavior
  const categories = document.getElementById('categories');
  categories.style.display = categories.style.display === 'none' || categories.style.display === '' ? 'block' : 'none';
});

// Mobile menu toggle
    document.getElementById('menu-btn').addEventListener('click', function() {
      var menu = document.getElementById('mobile-menu');
      menu.classList.toggle('hidden');
    });