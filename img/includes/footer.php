    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#">View Account</a></li>
                    <li><a href="#">Track Your Order</a></li>
                    <li><a href="#">Initiate Return/Exchange</a></li>
                    <li><a href="#">Gift Cards</a></li>
                    <li><a href="#">Promotions</a></li>
                    <li><a href="#">Customer Reviews</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Customer Care</h4>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Guest Order Lookup</a></li>
                    <li><a href="#">Return/Exchange Policy</a></li>
                    <li><a href="#">Shipping Policy</a></li>
                    <li><a href="#">Promo Terms</a></li>
                    <li><a href="#">International Orders</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Services</h4>
                <ul>
                    <li><a href="#">Mobile App</a></li>
                    <li><a href="#">Student Discount</a></li>
                    <li><a href="#">Healthcare Discount</a></li>
                    <li><a href="#">Military Discount</a></li>
                    <li><a href="#">Love Rewards</a></li>
                    <li><a href="#">Give $20, Get $20</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>About</h4>
                <ul>
                    <li><a href="#">Our Story</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Our Blog</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Accessibility</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Glamour Shopping. All rights reserved.</p>
        </div>
    </footer>

    <!-- Newsletter Signup Modal -->
    <div class="newsletter-modal" id="newsletterModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Get 15% off when you sign up for texts!</h3>
            <p>By signing up via text, you agree to receive recurring automated marketing texts from Glamour Shopping.</p>
            <div class="newsletter-form">
                <input type="tel" placeholder="Enter your phone number" class="phone-input" maxlength="9" pattern="[0-9]{9}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <button class="newsletter-btn">Text 'GLAMOUR' to 54858</button>
            </div>
        </div>
    </div>


    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/filters.js"></script>
    <?php if (isset($additional_js)) echo $additional_js; ?>
</body>
</html> 