<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: accessories.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Glamour Palace</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8fbff 0%, #e6f3ff 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .register-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 102, 204, 0.1);
            overflow: hidden;
            position: relative;
        }

        .register-header {
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .register-header h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .register-header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .register-form {
            padding: 40px 30px;
        }

        .form-row {
            display: block;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-group label.required::after {
            content: ' *';
            color: #dc3545;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8fbff;
        }

        .form-input:focus {
            outline: none;
            border-color: #0066cc;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .input-group {
            position: relative;
        }

        .input-group .form-input {
            padding-right: 50px;
        }

        .input-group .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .input-group .toggle-password:hover {
            color: #0066cc;
        }

        .form-select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8fbff;
            cursor: pointer;
        }

        .form-select:focus {
            outline: none;
            border-color: #0066cc;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 102, 204, 0.3);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e1e8ed;
        }

        .divider span {
            background: white;
            padding: 0 20px;
            color: #666;
            font-size: 0.9rem;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #0066cc;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            display: none;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0066cc;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .password-strength {
            margin-top: 8px;
            font-size: 0.8rem;
        }

        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }

        @media (max-width: 768px) {
            .register-container {
                margin: 10px;
                border-radius: 15px;
            }

            .register-header {
                padding: 30px 20px;
            }

            .register-form {
                padding: 30px 20px;
            }

            .register-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <button class="back-btn" onclick="window.location.href='accessories.php'">
            <i class="fas fa-arrow-left"></i>
        </button>

        <div class="register-header">
            <h1>Join Glamour Palace</h1>
            <p>Create your account and start shopping</p>
        </div>

        <div class="register-form">
            <div id="message" class="message"></div>

            <form id="registerForm">
                <div class="form-group">
                    <label for="username" class="required">Username</label>
                    <input type="text" id="username" name="username" class="form-input" 
                           placeholder="Choose a username" pattern="[a-zA-Z\s]+" title="Username must contain only letters and spaces (for names)" required>
                </div>

                <div class="form-group">
                    <label for="email" class="required">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" 
                           placeholder="your.email@example.com" required>
                </div>

                <div class="form-group">
                    <label for="contact_number" class="required">Contact Number</label>
                    <div class="contact-input-container" style="display: flex; align-items: center; border: 2px solid #e9ecef; border-radius: 8px; overflow: hidden;">
                        <div class="flag-prefix" style="display: flex; align-items: center; background: #f8f9fa; padding: 12px; border-right: 2px solid #e9ecef;">
                            <img src="/Glamour-system/img/flag.jpg" alt="Somali Flag" style="width: 20px; height: 15px; margin-right: 8px; border-radius: 2px;">
                            <span class="country-code" style="color: #666; font-weight: 500;">+252</span>
                        </div>
                        <input type="tel" id="contact_number" name="contact_number" class="form-input" 
                               placeholder="XXX XXX XXXX" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required style="flex: 1; border: none; padding: 12px; outline: none;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="gender" class="required">Gender</label>
                    <select id="gender" name="gender" class="form-select" required>
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="region" class="required">Region</label>
                    <select id="region" name="region" class="form-select" required>
                        <option value="">Select region</option>
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
                    <label for="city" class="required">City</label>
                    <select id="city" name="city" class="form-select" required disabled>
                        <option value="">Select region first</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password" class="required">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-input" 
                               placeholder="Create a password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div id="password-strength" class="password-strength"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="required">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" 
                               placeholder="Confirm your password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </form>

            <div class="divider">
                <span>or</span>
            </div>

            <div class="login-link">
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
            </div>
        </div>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Creating your account...</p>
        </div>
    </div>

    <script>
        // Username input validation - only allow letters and spaces
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('username');
            if (usernameInput) {
                usernameInput.addEventListener('input', function(e) {
                    // Remove any non-alphabetic characters and spaces
                    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
                });
                
                // Prevent pasting non-alphabetic characters
                usernameInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const paste = (e.clipboardData || window.clipboardData).getData('text');
                    const cleanPaste = paste.replace(/[^a-zA-Z\s]/g, '');
                    this.value = cleanPaste;
                });
            }
        });

        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleBtn = passwordInput.parentElement.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleBtn.className = 'fas fa-eye';
            }
        }

        function checkPasswordStrength(password) {
            const strengthDiv = document.getElementById('password-strength');
            let strength = 0;
            let message = '';

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            switch (strength) {
                case 0:
                case 1:
                    message = 'Very weak';
                    strengthDiv.className = 'password-strength strength-weak';
                    break;
                case 2:
                    message = 'Weak';
                    strengthDiv.className = 'password-strength strength-weak';
                    break;
                case 3:
                    message = 'Medium';
                    strengthDiv.className = 'password-strength strength-medium';
                    break;
                case 4:
                    message = 'Strong';
                    strengthDiv.className = 'password-strength strength-strong';
                    break;
                case 5:
                    message = 'Very strong';
                    strengthDiv.className = 'password-strength strength-strong';
                    break;
            }

            strengthDiv.textContent = message;
        }

        function showMessage(message, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = message;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
            
            if (type === 'success') {
                setTimeout(() => {
                    window.location.href = 'accessories.php';
                }, 2000);
            }
        }

        function hideMessage() {
            document.getElementById('message').style.display = 'none';
        }

        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        // Password confirmation checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Region-City connection
        const regionSelect = document.getElementById('region');
        const citySelect = document.getElementById('city');

        // Cities for each region
        const citiesByRegion = {
            'awdal': ['Borama', 'Zeila', 'Lughaya'],
            'woqooyi-galbeed': ['Hargeisa', 'Berbera', 'Gabiley'],
            'togdheer': ['Burao', 'Oodweyne', 'Balidhiig'],
            'sanaag': ['Erigavo', 'Badhan', 'Las Qoray'],
            'sool': ['Las Anod', 'Taleh', 'Hudun'],
            'bari': ['Bosaso', 'Qandala', 'Iskushuban'],
            'nugaal': ['Garowe', 'Burtinle', 'Eyl'],
            'mudug': ['Galkayo', 'Hobyo', 'Jariban', 'Garacad', 'Goldogob', 'Bacaadwayn'],
            'galguduud': ['Dhusamareb', 'Guriceel', 'Cadaado', 'Balanbale'],
            'hiran': ['Beledweyne', 'Bulo Burte', 'Jalalaqsi'],
            'shabeellaha-dhexe': ['Jowhar', 'Balcad', 'Mahaday'],
            'banadir': ['Abdiaziz', 'Bondhere', 'Daynile', 'Dharkenley', 'Hamar-Jajab', 'Hamar-Weyne', 'Hodan', 'Howlwadag', 'Karaan', 'Kaxda', 'Shangani', 'Shibis', 'Waberi', 'Wadajir', 'Wardhigley', 'Yaqshid'],
            'shabeellaha-hoose': ['Marka', 'Afgoye', 'Qoryoley', 'Barawe'],
            'bay': ['Baidoa', 'Burhakaba', 'Dinsoor'],
            'bakool': ['Hudur', 'Tayeeglow', 'Wajid', 'El Barde'],
            'gedo': ['Garbahaarreey', 'Bardera', 'Luuq', 'Beled Hawo', 'Dolow'],
            'jubbada-dhexe': ['Bu\'aale', 'Sakow', 'Jilib'],
            'jubbada-hoose': ['Kismayo', 'Afmadow', 'Dhobley', 'Jamaame']
        };

        regionSelect.addEventListener('change', function() {
            const selectedRegion = this.value;
            citySelect.innerHTML = '<option value="">Select city</option>';
            citySelect.disabled = true;

            if (selectedRegion && citiesByRegion[selectedRegion]) {
                citiesByRegion[selectedRegion].forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.toLowerCase().replace(/\s+/g, '-');
                    option.textContent = city;
                    citySelect.appendChild(option);
                });
                citySelect.disabled = false;
            }
        });

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('loading');
            const originalText = submitBtn.innerHTML;
            
            // Get form data
            const formData = new FormData(this);
            const data = {
                username: formData.get('username'),
                email: formData.get('email'),
                contact_number: formData.get('contact_number'),
                gender: formData.get('gender'),
                region: formData.get('region'),
                city: formData.get('city'),
                password: formData.get('password'),
                confirm_password: formData.get('confirm_password')
            };
            
            try {
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
                loading.style.display = 'block';
                hideMessage();
                
                // Send registration request
                const response = await fetch('/Glamour-system/auth/register-handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                } else {
                    showMessage(result.message, 'error');
                }
                
            } catch (error) {
                console.error('Registration error:', error);
                showMessage('An error occurred. Please try again.', 'error');
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                loading.style.display = 'none';
            }
        });

        // Auto-hide messages after 5 seconds
        setTimeout(() => {
            hideMessage();
        }, 5000);
    </script>
</body>
</html>
