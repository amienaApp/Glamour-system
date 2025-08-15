// Simple Script
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');
    
    // Initialize category modal functionality
    initializeCategoryModals();
    
    // Initialize header modals functionality
    initializeHeaderModals();
    
    // Quick View functionality
    const sidebar = document.getElementById('quick-view-sidebar');
    const overlay = document.getElementById('quick-view-overlay');
    const closeBtn = document.getElementById('close-quick-view');
    
    console.log('Quick view elements found:', {
        sidebar: !!sidebar,
        overlay: !!overlay,
        closeBtn: !!closeBtn
    });
    
    // Quick View button click handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quick-view') || e.target.closest('.quick-view')) {
            e.preventDefault();
            console.log('Quick view button clicked!');
            const button = e.target.classList.contains('quick-view') ? e.target : e.target.closest('.quick-view');
            const productId = button.getAttribute('data-product-id');
            console.log('Product ID:', productId);
            if (productId) {
                openQuickView(productId);
            } else {
                console.error('No product ID found');
            }
        }
    });
    
    // Close quick view
    if (closeBtn) closeBtn.addEventListener('click', closeQuickView);
    if (overlay) overlay.addEventListener('click', closeQuickView);
    
    function openQuickView(productId) {
        console.log('Opening quick view for:', productId);
        
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (!productCard) return;
        
        const name = productCard.querySelector('.product-name')?.textContent || 'Product';
        const price = productCard.querySelector('.product-price')?.textContent || '$0';
        
        // Update quick view content
        const titleEl = document.getElementById('quick-view-title');
        const priceEl = document.getElementById('quick-view-price');
        
        if (titleEl) titleEl.textContent = name;
        if (priceEl) priceEl.textContent = price;
        
        // Handle images
        const mainImage = document.getElementById('quick-view-main-image');
        const thumbnailsContainer = document.getElementById('quick-view-thumbnails');
        
        if (mainImage && thumbnailsContainer) {
            // Get all media from the product card (both images and videos)
            const mediaElements = productCard.querySelectorAll('.image-slider img, .image-slider video');
            
            if (mediaElements.length > 0) {
                const firstMedia = mediaElements[0];
                const isVideo = firstMedia.tagName.toLowerCase() === 'video';
                
                // Set main media
                if (isVideo) {
                    mainImage.style.display = 'none';
                    const mainVideo = document.getElementById('quick-view-main-video');
                    if (mainVideo) {
                        mainVideo.src = firstMedia.src;
                        mainVideo.style.display = 'block';
                        mainVideo.muted = true;
                        mainVideo.loop = true;
                        mainVideo.play().catch(e => console.log('Video autoplay prevented:', e));
                    }
                } else {
                    mainImage.src = firstMedia.src;
                    mainImage.alt = firstMedia.alt || name;
                    mainImage.style.display = 'block';
                    const mainVideo = document.getElementById('quick-view-main-video');
                    if (mainVideo) mainVideo.style.display = 'none';
                }
                
                // Clear and populate thumbnails
                thumbnailsContainer.innerHTML = '';
                
                mediaElements.forEach((media, index) => {
                    const thumbnail = document.createElement('div');
                    thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
                    const isThumbnailVideo = media.tagName.toLowerCase() === 'video';
                    
                    if (isThumbnailVideo) {
                        const thumbnailVideo = document.createElement('video');
                        thumbnailVideo.src = media.src;
                        thumbnailVideo.muted = true;
                        thumbnailVideo.setAttribute('data-index', index);
                        thumbnail.appendChild(thumbnailVideo);
                    } else {
                        const thumbnailImg = document.createElement('img');
                        thumbnailImg.src = media.src;
                        thumbnailImg.alt = media.alt;
                        thumbnailImg.setAttribute('data-index', index);
                        thumbnail.appendChild(thumbnailImg);
                    }
                    
                    thumbnail.addEventListener('click', () => {
                        if (isThumbnailVideo) {
                            // Show video
                            mainImage.style.display = 'none';
                            const mainVideo = document.getElementById('quick-view-main-video');
                            if (mainVideo) {
                                mainVideo.src = media.src;
                                mainVideo.style.display = 'block';
                                mainVideo.muted = true;
                                mainVideo.loop = true;
                                mainVideo.play().catch(e => console.log('Video autoplay prevented:', e));
                            }
                        } else {
                            // Show image
                            mainImage.src = media.src;
                            mainImage.alt = media.alt;
                            mainImage.style.display = 'block';
                            const mainVideo = document.getElementById('quick-view-main-video');
                            if (mainVideo) mainVideo.style.display = 'none';
                        }
                        
                        thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                        thumbnail.classList.add('active');
                    });
                    
                    thumbnailsContainer.appendChild(thumbnail);
                });
            }
        }
        
        // Handle color circles in quick view
        const colorSelection = document.getElementById('quick-view-color-selection');
        if (colorSelection) {
            colorSelection.innerHTML = '';
            
            // Get all color circles from the product card
            const colorCircles = productCard.querySelectorAll('.color-circle');
            
            colorCircles.forEach(circle => {
                const color = circle.getAttribute('data-color');
                const title = circle.getAttribute('title');
                const isActive = circle.classList.contains('active');
                
                if (color) {
                    const colorBtn = document.createElement('div');
                    colorBtn.className = 'quick-view-color-circle';
                    colorBtn.style.cssText = `
                        width: 36px;
                        height: 36px;
                        border-radius: 50%;
                        background-color: ${color};
                        border: 2px solid ${isActive ? '#000' : '#ddd'};
                        cursor: pointer;
                        margin: 0 5px;
                        display: inline-block;
                        transition: all 0.3s ease;
                        position: relative;
                        z-index: 10;
                    `;
                    colorBtn.title = title || color;
                    
                    if (isActive) {
                        colorBtn.classList.add('active');
                    }
                    
                    // Add click handler for quick view color circles
                    colorBtn.addEventListener('click', function() {
                        // Remove active class from all color options
                        colorSelection.querySelectorAll('.quick-view-color-circle').forEach(opt => {
                            opt.classList.remove('active');
                            opt.style.border = '2px solid #ddd';
                        });
                        
                        // Add active class to clicked option
                        this.classList.add('active');
                        this.style.border = '2px solid #000';
                        
                        // Update media for this color
                        const mediaForColor = productCard.querySelectorAll(`.image-slider img[data-color="${color}"], .image-slider video[data-color="${color}"]`);
                        
                        if (mediaForColor.length > 0 && mainImage && thumbnailsContainer) {
                            const firstMedia = mediaForColor[0];
                            const isVideo = firstMedia.tagName.toLowerCase() === 'video';
                            
                            // Update main media
                            if (isVideo) {
                                mainImage.style.display = 'none';
                                const mainVideo = document.getElementById('quick-view-main-video');
                                if (mainVideo) {
                                    mainVideo.src = firstMedia.src;
                                    mainVideo.style.display = 'block';
                                    mainVideo.muted = true;
                                    mainVideo.loop = true;
                                    mainVideo.play().catch(e => console.log('Video autoplay prevented:', e));
                                }
                            } else {
                                mainImage.src = firstMedia.src;
                                mainImage.alt = firstMedia.alt || name;
                                mainImage.style.display = 'block';
                                const mainVideo = document.getElementById('quick-view-main-video');
                                if (mainVideo) mainVideo.style.display = 'none';
                            }
                            
                            // Update thumbnails
                            thumbnailsContainer.innerHTML = '';
                            mediaForColor.forEach((media, index) => {
                                const thumbnail = document.createElement('div');
                                thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
                                const isThumbnailVideo = media.tagName.toLowerCase() === 'video';
                                
                                if (isThumbnailVideo) {
                                    const thumbnailVideo = document.createElement('video');
                                    thumbnailVideo.src = media.src;
                                    thumbnailVideo.muted = true;
                                    thumbnailVideo.setAttribute('data-index', index);
                                    thumbnail.appendChild(thumbnailVideo);
                                } else {
                                    const thumbnailImg = document.createElement('img');
                                    thumbnailImg.src = media.src;
                                    thumbnailImg.alt = media.alt;
                                    thumbnailImg.setAttribute('data-index', index);
                                    thumbnail.appendChild(thumbnailImg);
                                }
                                
                                thumbnail.addEventListener('click', () => {
                                    if (isThumbnailVideo) {
                                        // Show video
                                        mainImage.style.display = 'none';
                                        const mainVideo = document.getElementById('quick-view-main-video');
                                        if (mainVideo) {
                                            mainVideo.src = media.src;
                                            mainVideo.style.display = 'block';
                                            mainVideo.muted = true;
                                            mainVideo.loop = true;
                                            mainVideo.play().catch(e => console.log('Video autoplay prevented:', e));
                                        }
                                    } else {
                                        // Show image
                                        mainImage.src = media.src;
                                        mainImage.alt = media.alt;
                                        mainImage.style.display = 'block';
                                        const mainVideo = document.getElementById('quick-view-main-video');
                                        if (mainVideo) mainVideo.style.display = 'none';
                                    }
                                    
                                    thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                                    thumbnail.classList.add('active');
                                });
                                
                                thumbnailsContainer.appendChild(thumbnail);
                            });
                        }
                    });
                    
                    colorSelection.appendChild(colorBtn);
                }
            });
        }
        
        // Handle size selection in quick view
        const sizeSelection = document.getElementById('quick-view-size-selection');
        if (sizeSelection) {
            sizeSelection.innerHTML = '';
            
            // Default sizes
            const sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
            
            sizes.forEach(size => {
                const sizeBtn = document.createElement('div');
                sizeBtn.className = 'size-option';
                sizeBtn.textContent = size;
                sizeBtn.style.cssText = `
                    padding: 8px 12px;
                    border: 2px solid #ddd;
                    border-radius: 4px;
                    cursor: pointer;
                    margin: 0 5px 5px 0;
                    display: inline-block;
                    transition: all 0.3s ease;
                    font-size: 14px;
                    font-weight: 500;
                `;
                
                // Add click handler
                sizeBtn.addEventListener('click', function() {
                    // Remove active class from all size options
                    sizeSelection.querySelectorAll('.size-option').forEach(opt => {
                        opt.style.border = '2px solid #ddd';
                        opt.style.backgroundColor = 'transparent';
                        opt.style.color = '#333';
                    });
                    
                    // Add active class to clicked option
                    this.style.border = '2px solid #333';
                    this.style.backgroundColor = '#333';
                    this.style.color = 'white';
                });
                
                sizeSelection.appendChild(sizeBtn);
            });
        }
        
        // Show quick view
        console.log('Showing quick view...');
        if (sidebar) {
            sidebar.classList.add('active');
            console.log('Sidebar active class added');
        } else {
            console.error('Sidebar not found');
        }
        if (overlay) {
            overlay.classList.add('active');
            console.log('Overlay active class added');
        } else {
            console.error('Overlay not found');
        }
        document.body.style.overflow = 'hidden';
        console.log('Quick view should be visible now');
    }
    
    function closeQuickView() {
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // Color circle functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('color-circle')) {
            e.preventDefault();
            e.stopPropagation();
            
            const selectedColor = e.target.getAttribute('data-color');
            const productCard = e.target.closest('.product-card');
            
            if (productCard) {
                const imageSlider = productCard.querySelector('.image-slider');
                const colorCircles = productCard.querySelectorAll('.color-circle');
                
                // Remove active class from all color circles
                colorCircles.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked circle
                e.target.classList.add('active');
                
                // Show media for this color
                if (imageSlider) {
                    const mediaForColor = imageSlider.querySelectorAll(`img[data-color="${selectedColor}"], video[data-color="${selectedColor}"]`);
                    
                    if (mediaForColor.length > 0) {
                        // Hide all media first
                        imageSlider.querySelectorAll('img, video').forEach(media => {
                            media.classList.remove('active');
                            media.style.opacity = '0';
                        });
                        
                        // Show first media for this color (front view)
                        const firstMedia = mediaForColor[0];
                        firstMedia.classList.add('active');
                        firstMedia.style.opacity = '1';
                        
                        // Auto-play video if it's a video
                        if (firstMedia.tagName.toLowerCase() === 'video') {
                            firstMedia.play().catch(e => console.log('Video autoplay prevented:', e));
                        }
                        
                        // Now you can click on the media to see back view
                        // The click handler will cycle through all media for this color
                        console.log(`Showing ${mediaForColor.length} media items for color: ${selectedColor}`);
                        console.log('Click on the media to see back view');
                    }
                }
            }
        }
    });
    
    // Media click to switch between front/back
    document.addEventListener('click', function(e) {
        if (e.target.closest('.image-slider') && (e.target.tagName === 'IMG' || e.target.tagName === 'VIDEO')) {
            const clickedMedia = e.target;
            const imageSlider = clickedMedia.closest('.image-slider');
            const productCard = imageSlider.closest('.product-card');
            
            if (imageSlider && productCard) {
                const activeColorCircle = productCard.querySelector('.color-circle.active');
                if (!activeColorCircle) return;
                
                const activeColor = activeColorCircle.getAttribute('data-color');
                const allMedia = imageSlider.querySelectorAll('img, video');
                const mediaForColor = Array.from(allMedia).filter(media => {
                    return media.getAttribute('data-color') === activeColor;
                });
                
                if (mediaForColor.length > 1) {
                    const currentIndex = mediaForColor.indexOf(clickedMedia);
                    const nextIndex = (currentIndex + 1) % mediaForColor.length;
                    const nextMedia = mediaForColor[nextIndex];
                    
                    console.log(`Switching from media ${currentIndex + 1} to ${nextIndex + 1} of ${mediaForColor.length}`);
                    
                    // Hide all media
                    allMedia.forEach(media => {
                        media.classList.remove('active');
                        media.style.opacity = '0';
                    });
                    
                    // Show the next media
                    nextMedia.classList.add('active');
                    nextMedia.style.opacity = '1';
                    
                    // Auto-play video if it's a video
                    if (nextMedia.tagName.toLowerCase() === 'video') {
                        nextMedia.play().catch(e => console.log('Video autoplay prevented:', e));
                    }
                } else {
                    console.log('Only one media item for this color, no switching possible');
                }
            }
        }
    });
    
    // Initialize product cards
    function initializeProductCard(card) {
        const colorCircles = card.querySelectorAll('.color-circle');
        const imageSlider = card.querySelector('.image-slider');
        
        // Ensure first media is visible
        if (imageSlider) {
            const firstMedia = imageSlider.querySelector('img, video');
            if (firstMedia) {
                firstMedia.classList.add('active');
                firstMedia.style.opacity = '1';
                
                // Auto-play video if it's a video
                if (firstMedia.tagName.toLowerCase() === 'video') {
                    firstMedia.play().catch(e => console.log('Video autoplay prevented:', e));
                }
            }
        }
        
        // Ensure first color circle is active
        if (colorCircles.length > 0) {
            const firstColorCircle = colorCircles[0];
            if (!firstColorCircle.classList.contains('active')) {
                firstColorCircle.classList.add('active');
            }
        }
    }
    
    // Initialize existing product cards
    const existingProductCards = document.querySelectorAll('.product-card');
    existingProductCards.forEach(card => {
        initializeProductCard(card);
    });
    
    // Add to cart functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-to-bag') || e.target.closest('.add-to-bag')) {
            e.preventDefault();
            const button = e.target.classList.contains('add-to-bag') ? e.target : e.target.closest('.add-to-bag');
            
            if (button.disabled) return;
            
            const productCard = button.closest('.product-card');
            const productId = productCard ? productCard.getAttribute('data-product-id') : null;
            const productName = productCard ? productCard.querySelector('.product-name')?.textContent : 'Product';
            
            if (productId) {
                addToCart(productId, productName);
            }
        }
    });
    
    function addToCart(productId, productName) {
        console.log('Adding to cart:', productId, productName);
        
        // Create notification
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            font-weight: 500;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        notification.textContent = `✓ ${productName} added to cart!`;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    // Category Modal Functionality
    function initializeCategoryModals() {
        const categoryModals = document.querySelectorAll('.category-modal');
        const modalTriggers = document.querySelectorAll('.modal-trigger');
        
        // Handle subcategory item clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.subcategory-item')) {
                e.preventDefault();
                const subcategoryItem = e.target.closest('.subcategory-item');
                const category = subcategoryItem.getAttribute('data-category');
                const subcategory = subcategoryItem.getAttribute('data-subcategory');
                const href = subcategoryItem.getAttribute('href');
                
                console.log('Subcategory clicked:', category, subcategory);
                
                // Navigate to the section if href is provided
                if (href && href.startsWith('#')) {
                    const targetSection = document.querySelector(href);
                    if (targetSection) {
                        // Close all modals
                        categoryModals.forEach(modal => {
                            modal.style.opacity = '0';
                            modal.style.visibility = 'hidden';
                        });
                        
                        // Smooth scroll to the section
                        targetSection.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        console.log(`Navigating to section: ${href}`);
                    }
                } else if (subcategory) {
                    console.log(`Navigating to ${category} > ${subcategory}`);
                    // Fallback for other subcategories that don't have sections yet
                }
            }
        });
        
        // Add hover delay for better UX
        let hoverTimeout;
        
        modalTriggers.forEach(trigger => {
            const modal = trigger.nextElementSibling;
            
            trigger.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(() => {
                    // Close all other modals
                    categoryModals.forEach(m => {
                        if (m !== modal) {
                            m.style.opacity = '0';
                            m.style.visibility = 'hidden';
                        }
                    });
                    
                    // Show current modal
                    modal.style.opacity = '1';
                    modal.style.visibility = 'visible';
                }, 150); // Small delay for better UX
            });
            
            trigger.addEventListener('mouseleave', function() {
                clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(() => {
                    modal.style.opacity = '0';
                    modal.style.visibility = 'hidden';
                }, 200); // Slightly longer delay to prevent flickering
            });
            
            // Keep modal open when hovering over it
            modal.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
            });
            
            modal.addEventListener('mouseleave', function() {
                hoverTimeout = setTimeout(() => {
                    modal.style.opacity = '0';
                    modal.style.visibility = 'hidden';
                }, 100);
            });
        });
        
        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-item-modal')) {
                categoryModals.forEach(modal => {
                    modal.style.opacity = '0';
                    modal.style.visibility = 'hidden';
                });
            }
        });
    }
    
    // Header Modals Functionality
    function initializeHeaderModals() {
        // Get modal elements
        const userModal = document.getElementById('user-modal');
        const userIcon = document.getElementById('user-icon');
        const flagContainer = document.querySelector('.flag-container');
        
        // Get close buttons
        const closeUserModal = document.getElementById('close-user-modal');
        
        // User Icon Click - Open User Modal
        if (userIcon) {
            userIcon.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(userModal);
            });
        }
        
        // Flag Container Click - Disabled (Coming Soon)
        if (flagContainer) {
            flagContainer.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Region settings coming soon!');
            });
        }
        
        // Close User Modal
        if (closeUserModal) {
            closeUserModal.addEventListener('click', function() {
                closeModal(userModal);
            });
        }
        
        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                closeModal(e.target);
            }
        });
        
        // Show/Hide Password functionality
        const showPasswordBtns = document.querySelectorAll('.show-password');
        
        showPasswordBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const passwordInput = this.previousElementSibling;
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    passwordInput.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            });
        });
        
        // Region-City Dynamic Dropdown
        const regionSelect = document.getElementById('region');
        const citySelect = document.getElementById('city');
        
        if (regionSelect && citySelect) {
            regionSelect.addEventListener('change', function() {
                const selectedRegion = this.value;
                citySelect.innerHTML = '<option value="">Select City</option>';
                citySelect.disabled = true;
                
                if (selectedRegion) {
                    const cities = getCitiesForRegion(selectedRegion);
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.toLowerCase().replace(/\s+/g, '-');
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                }
            });
        }
        
        // User Registration Form Submission
        const registrationForm = document.querySelector('.user-registration-form');
        
        if (registrationForm) {
            registrationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = {
                    username: document.getElementById('username').value,
                    email: document.getElementById('email').value,
                    contactNumber: document.getElementById('contact-number').value,
                    gender: document.querySelector('input[name="gender"]:checked')?.value || '',
                    region: document.getElementById('region').value,
                    city: document.getElementById('city').value,
                    password: document.getElementById('password').value,
                    confirmPassword: document.getElementById('confirm-password').value,
                    terms: document.getElementById('terms').checked,
                    newsletter: document.getElementById('newsletter').checked
                };
                
                // Validation
                if (!formData.terms) {
                    alert('Please agree to the Terms of Service and Privacy Policy');
                    return;
                }
                
                if (formData.password !== formData.confirmPassword) {
                    alert('Passwords do not match!');
                    return;
                }
                
                if (!formData.gender) {
                    alert('Please select your gender');
                    return;
                }
                
                if (formData.password.length < 6) {
                    alert('Password must be at least 6 characters long');
                    return;
                }
                
                console.log('Registration data:', formData);
                alert('Account created successfully! Welcome to Glamour Palace!');
                closeModal(userModal);
                
                // Reset form
                registrationForm.reset();
                citySelect.disabled = true;
                citySelect.innerHTML = '<option value="">Select Region First</option>';
            });
        }
        
        // Helper functions
        function openModal(modal) {
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        }
        
        function closeModal(modal) {
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = ''; // Restore scrolling
            }
        }
        
        function getCitiesForRegion(region) {
            const cityMap = {
                'banadir': ['Mogadishu', 'Afgooye', 'Marka'],
                'bari': ['Bosaso', 'Qardho', 'Caluula', 'Bandarbeyla'],
                'bay': ['Baidoa', 'Buurhakaba', 'Dinsor'],
                'galguduud': ['Dhusamareb', 'Guriceel', 'Abudwaq', 'Adado'],
                'gedo': ['Garbahaarrey', 'Bardheere', 'Luuq', 'El Wak'],
                'hiran': ['Beledweyne', 'Buloburde', 'Jalalaqsi'],
                'jubbada-dhexe': ['Bu\'aale', 'Jilib', 'Sakow'],
                'jubbada-hoose': ['Kismayo', 'Jamame', 'Badhaadhe'],
                'mudug': ['Galkayo', 'Hobyo', 'Garoowe'],
                'nugaal': ['Garowe', 'Eyl', 'Burtinle'],
                'sanaag': ['Erigavo', 'Badhan', 'Las Khorey'],
                'shabeellaha-dhexe': ['Jowhar', 'Balcad', 'Adale', 'Warsheikh'],
                'shabeellaha-hoose': ['Marka', 'Wanlaweyn', 'Qoryooley', 'Baraawe'],
                'sool': ['Las Anod', 'Taleex', 'Xingalool'],
                'togdheer': ['Burao', 'Oodweyne', 'Sheikh'],
                'woqooyi-galbeed': ['Hargeisa', 'Berbera', 'Borama', 'Gabiley']
            };
            
            return cityMap[region] || [];
        }
    }
});
