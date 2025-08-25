
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
    

<<<<<<< HEAD
// Check if categoryLink element exists before adding event listener
=======
// Category link toggle
>>>>>>> 026227e30f69d7328596d1585a8495130bac8bf4
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
<<<<<<< HEAD
    const menuBtn = document.getElementById('menu-btn');
    if (menuBtn) {
      menuBtn.addEventListener('click', function() {
        var menu = document.getElementById('mobile-menu');
        if (menu) {
          menu.classList.toggle('hidden');
        }
      });
    }

// Debug: Check if Categories link is present
document.addEventListener('DOMContentLoaded', function() {
  const categoriesLink = document.querySelector('.nav-item.dropdown .nav-link');
  if (categoriesLink) {
    console.log('Categories link found:', categoriesLink.textContent);
  } else {
    console.log('Categories link not found');
  }
});
=======
const menuBtn = document.getElementById('menu-btn');
if (menuBtn) {
  menuBtn.addEventListener('click', function() {
    var menu = document.getElementById('mobile-menu');
    if (menu) {
      menu.classList.toggle('hidden');
    }
  });
}
>>>>>>> 026227e30f69d7328596d1585a8495130bac8bf4
