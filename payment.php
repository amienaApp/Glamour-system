<?php
/**
 * Payment Page
 * Displays payment methods and forms for Somali payment options
 */

session_start();
require_once 'models/Payment.php';
require_once 'models/Order.php';
require_once 'models/Cart.php';

// Include cart configuration for consistent user ID
if (file_exists('cart-config.php')) {
    require_once 'cart-config.php';
}

// Get order details if order_id is provided
$orderId = $_GET['order_id'] ?? null;
$userId = getCartUserId(); // Use the consistent user ID from cart config




$order = null;
$cart = null;

if ($orderId) {
    $orderModel = new Order();
    $order = $orderModel->getById($orderId);
    

} else {
    // If no order, get cart total
    $cartModel = new Cart();
    $cart = $cartModel->getCart($userId);
    

}

$amount = 0;
if ($order && isset($order['total_amount'])) {
    $amount = $order['total_amount'];
} elseif ($cart && isset($cart['total'])) {
    $amount = $cart['total'];
}





// If amount is still 0, try to get cart total as fallback
if ($amount == 0 && !$cart) {
    $cartModel = new Cart();
    $cart = $cartModel->getCart($userId);
    if ($cart && isset($cart['total']) && $cart['total'] > 0) {
        $amount = $cart['total'];
    }
}

// If amount is still 0, redirect to cart page with message
if ($amount == 0) {
    header('Location: cart-unified.php?message=empty_cart');
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Glamour System</title>
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
        }

        .header h1 {
            color: #0066cc;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1rem;
        }

        .amount-display {
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0, 102, 204, 0.2);
        }

        .amount-display h2 {
            font-size: 2rem;
            margin-bottom: 5px;
        }

        .amount-display p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 25px;
            margin-bottom: 30px;
        }

        .payment-method {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
            border: 2px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .payment-method::before {
            content: 'Click to select';
            position: absolute;
            top: 10px;
            right: 15px;
            background: #0066cc;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .payment-method:hover::before {
            opacity: 1;
        }

        .payment-method:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 102, 204, 0.15);
            border-color: #b3d9ff;
        }

        .payment-method.selected {
            border-color: #0066cc;
            background: linear-gradient(135deg, #f8fbff 0%, #e6f3ff 100%);
        }

        .payment-method.selected::before {
            content: 'Selected';
            background: #28a745;
        }

        .method-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .method-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .method-info h3 {
            color: #333;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .method-info p {
            color: #666;
            font-size: 0.9rem;
        }

        .payment-form {
            display: none;
            margin-top: 20px;
        }

        .payment-form.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
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

        .phone-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .country-code {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 15px;
            background: #f8fbff;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-weight: 600;
            color: #0066cc;
            min-width: 120px;
        }

        .flag-icon {
            width: 20px;
            height: 15px;
            background: linear-gradient(to bottom, #0066cc 50%, #ffffff 50%);
            border-radius: 2px;
            position: relative;
        }

        .flag-icon::after {
            content: '★';
            position: absolute;
            top: -2px;
            left: 2px;
            color: #ffffff;
            font-size: 8px;
        }

        .phone-input {
            flex: 1;
        }

        .card-inputs {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 15px;
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

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 25px;
            background: white;
            color: #0066cc;
            border: 2px solid #0066cc;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background: #0066cc;
            color: white;
            transform: translateY(-2px);
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 10px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background: #28a745;
        }

        .notification.error {
            background: #dc3545;
        }

        .notification.info {
            background: #17a2b8;
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

        .form-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #e1e8ed;
        }

        .form-section h4 {
            color: #0066cc;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .required-field::after {
            content: ' *';
            color: #dc3545;
        }

        .help-text {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .payment-methods {
                grid-template-columns: 1fr;
            }
            
            .card-inputs {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 2rem;
            }

            .phone-input-group {
                flex-direction: column;
                align-items: stretch;
            }

            .country-code {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
                    <a href="cart-unified.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Cart
        </a>

        <div class="header">
            <h1>Secure Payment</h1>
            <p>Choose your preferred payment method</p>
        </div>

        <div class="amount-display">
            <h2>$<?php echo number_format($amount, 2); ?></h2>
            <p>Total Amount to Pay</p>

        </div>

        <div class="payment-methods" id="paymentMethods">
            <!-- Payment methods will be loaded here -->
        </div>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Processing payment...</p>
        </div>
    </div>

    <div id="notification" class="notification"></div>

    <script>
        let selectedMethod = null;
        let paymentMethods = [];

        // Load payment methods
        async function loadPaymentMethods() {
            try {
                const response = await fetch('payment-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'get_payment_methods'
                    })
                });

                const data = await response.json();
                if (data.success) {
                    paymentMethods = data.methods;
                    renderPaymentMethods();
                }
            } catch (error) {
                showNotification('Error loading payment methods', 'error');
            }
        }

        // Render payment methods
        function renderPaymentMethods() {
            const container = document.getElementById('paymentMethods');
            container.innerHTML = '';

            paymentMethods.forEach(method => {
                const methodElement = document.createElement('div');
                methodElement.className = 'payment-method';
                methodElement.setAttribute('data-method', method.id);

                methodElement.innerHTML = `
                    <div class="method-header">
                        <div class="method-icon" style="background: ${method.color}">
                            <i class="${method.icon}"></i>
                        </div>
                        <div class="method-info">
                            <h3>${method.name}</h3>
                            <p>${method.description}</p>
                        </div>
                    </div>
                    <div class="payment-form" id="form-${method.id}">
                        ${generatePaymentForm(method)}
                    </div>
                `;

                container.appendChild(methodElement);
            });

            // Add click event listeners after rendering
            document.querySelectorAll('.payment-method').forEach(element => {
                element.addEventListener('click', function(e) {
                    // Don't trigger if clicking on form elements
                    if (e.target.closest('.payment-form') || e.target.closest('.submit-btn')) {
                        return;
                    }
                    const methodId = this.getAttribute('data-method');
                    selectPaymentMethod(methodId);
                });
            });
        }

        // Generate payment form based on method
        function generatePaymentForm(method) {
            switch (method.id) {
                case 'waafi':
                    return `
                        <div class="form-section">
                            <h4><i class="fas fa-user"></i> Personal Information</h4>
                            <div class="form-group">
                                <label for="fullname-${method.id}" class="required-field">Full Name</label>
                                <input type="text" id="fullname-${method.id}" class="form-input" 
                                       placeholder="Enter your full name" required>
                            </div>
                            <div class="form-group">
                                <label for="email-${method.id}" class="required-field">Email Address</label>
                                <input type="email" id="email-${method.id}" class="form-input" 
                                       placeholder="your.email@example.com" required>
                            </div>
                            <div class="form-group">
                                <label for="region-${method.id}" class="required-field">Region</label>
                                <select id="region-${method.id}" class="form-input" required onchange="updateCities('${method.id}')">
                                    <option value="">Select Region</option>
                                    <option value="banadir">Banadir</option>
                                    <option value="bari">Bari</option>
                                    <option value="bay">Bay</option>
                                    <option value="galkacyo">Galkacyo</option>
                                    <option value="gedo">Gedo</option>
                                    <option value="hiran">Hiran</option>
                                    <option value="jubaland">Jubaland</option>
                                    <option value="mudug">Mudug</option>
                                    <option value="nugal">Nugal</option>
                                    <option value="sanaag">Sanaag</option>
                                    <option value="shabeellaha_dhexe">Shabeellaha Dhexe</option>
                                    <option value="shabeellaha_hoose">Shabeellaha Hoose</option>
                                    <option value="sool">Sool</option>
                                    <option value="togdheer">Togdheer</option>
                                    <option value="woqooyi_galbeed">Woqooyi Galbeed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="city-${method.id}" class="required-field">City</label>
                                <select id="city-${method.id}" class="form-input" required>
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product-description-${method.id}">Product Description</label>
                                <textarea id="product-description-${method.id}" class="form-input" 
                                          rows="3" placeholder="Describe your products or any special requirements..."></textarea>
                                <div class="help-text">Optional: Add details about your products or special instructions</div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4><i class="fas fa-mobile-alt"></i> Mobile Money Details</h4>
                            <div class="form-group">
                                <label for="phone-${method.id}" class="required-field">Phone Number</label>
                                <div class="phone-input-group">
                                    <div class="country-code">
                                        <div class="flag-icon"></div>
                                        +252
                                    </div>
                                                                         <input type="tel" id="phone-${method.id}" class="form-input phone-input" 
                                            placeholder="090 xxx xxxx" maxlength="10" required>
                                </div>
                                                                 <div class="help-text">
                                     Supports: SAHAL (090), SAAD (063), EVC (061), EDAHAB (066) - Enter 10 digits
                                 </div>
                            </div>
                        </div>
                        
                        <button class="submit-btn" onclick="processPayment('${method.id}')">
                            <i class="fas fa-mobile-alt"></i>
                            Pay with Waafi - $${<?php echo $amount; ?>}
                        </button>
                    `;
                
                case 'card':
                    return `
                        <div class="form-section">
                            <h4><i class="fas fa-user"></i> Personal Information</h4>
                            <div class="form-group">
                                <label for="fullname-${method.id}" class="required-field">Full Name</label>
                                <input type="text" id="fullname-${method.id}" class="form-input" 
                                       placeholder="Enter your full name" required>
                            </div>
                            <div class="form-group">
                                <label for="email-${method.id}" class="required-field">Email Address</label>
                                <input type="email" id="email-${method.id}" class="form-input" 
                                       placeholder="your.email@example.com" required>
                            </div>
                            <div class="form-group">
                                <label for="region-${method.id}" class="required-field">Region</label>
                                <select id="region-${method.id}" class="form-input" required onchange="updateCities('${method.id}')">
                                    <option value="">Select Region</option>
                                    <option value="banadir">Banadir</option>
                                    <option value="bari">Bari</option>
                                    <option value="bay">Bay</option>
                                    <option value="galkacyo">Galkacyo</option>
                                    <option value="gedo">Gedo</option>
                                    <option value="hiran">Hiran</option>
                                    <option value="jubaland">Jubaland</option>
                                    <option value="mudug">Mudug</option>
                                    <option value="nugal">Nugal</option>
                                    <option value="sanaag">Sanaag</option>
                                    <option value="shabeellaha_dhexe">Shabeellaha Dhexe</option>
                                    <option value="shabeellaha_hoose">Shabeellaha Hoose</option>
                                    <option value="sool">Sool</option>
                                    <option value="togdheer">Togdheer</option>
                                    <option value="woqooyi_galbeed">Woqooyi Galbeed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="city-${method.id}" class="required-field">City</label>
                                <select id="city-${method.id}" class="form-input" required>
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product-description-${method.id}">Product Description</label>
                                <textarea id="product-description-${method.id}" class="form-input" 
                                          rows="3" placeholder="Describe your products or any special requirements..."></textarea>
                                <div class="help-text">Optional: Add details about your products or special instructions</div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4><i class="fas fa-credit-card"></i> Card Information</h4>
                            <div class="form-group">
                                <label for="card-number" class="required-field">Card Number</label>
                                <input type="text" id="card-number" class="form-input" 
                                       placeholder="1234 5678 9012 3456" maxlength="19" required>
                                <div class="help-text">Visa, Mastercard, American Express</div>
                            </div>
                            <div class="card-inputs">
                                <div class="form-group">
                                    <label for="card-expiry" class="required-field">Expiry Date</label>
                                    <input type="text" id="card-expiry" class="form-input" 
                                           placeholder="MM/YY" maxlength="5" required>
                                </div>
                                <div class="form-group">
                                    <label for="card-cvv" class="required-field">CVV</label>
                                    <input type="text" id="card-cvv" class="form-input" 
                                           placeholder="123" maxlength="4" required>
                                </div>
                            </div>
                        </div>
                        
                        <button class="submit-btn" onclick="processPayment('card')">
                            <i class="fas fa-credit-card"></i>
                            Pay with Card - $${<?php echo $amount; ?>}
                        </button>
                    `;
                
                case 'bank':
                    return `
                        <div class="form-section">
                            <h4><i class="fas fa-user"></i> Personal Information</h4>
                            <div class="form-group">
                                <label for="fullname-${method.id}" class="required-field">Full Name</label>
                                <input type="text" id="fullname-${method.id}" class="form-input" 
                                       placeholder="Enter your full name" required>
                            </div>
                            <div class="form-group">
                                <label for="email-${method.id}" class="required-field">Email Address</label>
                                <input type="email" id="email-${method.id}" class="form-input" 
                                       placeholder="your.email@example.com" required>
                            </div>
                            <div class="form-group">
                                <label for="region-${method.id}" class="required-field">Region</label>
                                <select id="region-${method.id}" class="form-input" required onchange="updateCities('${method.id}')">
                                    <option value="">Select Region</option>
                                    <option value="banadir">Banadir</option>
                                    <option value="bari">Bari</option>
                                    <option value="bay">Bay</option>
                                    <option value="galkacyo">Galkacyo</option>
                                    <option value="gedo">Gedo</option>
                                    <option value="hiran">Hiran</option>
                                    <option value="jubaland">Jubaland</option>
                                    <option value="mudug">Mudug</option>
                                    <option value="nugal">Nugal</option>
                                    <option value="sanaag">Sanaag</option>
                                    <option value="shabeellaha_dhexe">Shabeellaha Dhexe</option>
                                    <option value="shabeellaha_hoose">Shabeellaha Hoose</option>
                                    <option value="sool">Sool</option>
                                    <option value="togdheer">Togdheer</option>
                                    <option value="woqooyi_galbeed">Woqooyi Galbeed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="city-${method.id}" class="required-field">City</label>
                                <select id="city-${method.id}" class="form-input" required>
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product-description-${method.id}">Product Description</label>
                                <textarea id="product-description-${method.id}" class="form-input" 
                                          rows="3" placeholder="Describe your products or any special requirements..."></textarea>
                                <div class="help-text">Optional: Add details about your products or special instructions</div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4><i class="fas fa-university"></i> Bank Transfer Details</h4>
                            <div class="form-group">
                                <label for="bank-name" class="required-field">Bank Name</label>
                                <input type="text" id="bank-name" class="form-input" 
                                       placeholder="Enter your bank name" required>
                            </div>
                            <div class="form-group">
                                <label for="account-number" class="required-field">Account Number</label>
                                <input type="text" id="account-number" class="form-input" 
                                       placeholder="Enter your account number" required>
                            </div>
                            <div class="form-group">
                                <label for="account-holder" class="required-field">Account Holder Name</label>
                                <input type="text" id="account-holder" class="form-input" 
                                       placeholder="Enter account holder name" required>
                            </div>
                        </div>
                        
                        <button class="submit-btn" onclick="processPayment('bank')">
                            <i class="fas fa-university"></i>
                            Initiate Bank Transfer - $${<?php echo $amount; ?>}
                        </button>
                    `;
                
                default:
                    return '';
            }
        }

        // Select payment method
        function selectPaymentMethod(methodId) {
            // Remove previous selection
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });

            // Hide all forms
            document.querySelectorAll('.payment-form').forEach(el => {
                el.classList.remove('active');
            });

            // Select new method
            const methodElement = document.querySelector(`[data-method="${methodId}"]`);
            if (methodElement) {
                methodElement.classList.add('selected');
                const form = methodElement.querySelector(`#form-${methodId}`);
                if (form) {
                    form.classList.add('active');
                }
            }

            selectedMethod = methodId;
        }

        // Process payment
        async function processPayment(methodId) {
            const loading = document.getElementById('loading');
            const submitBtn = event.target;
            const originalText = submitBtn.innerHTML;

            try {
                // Validate form
                if (!validateForm(methodId)) {
                    return;
                }

                loading.style.display = 'block';
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

                // Get payment data based on method
                const paymentData = getPaymentData(methodId);
                if (!paymentData) {
                    return;
                }

                // Create payment
                const createResponse = await fetch('payment-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'create_payment',
                        order_id: '<?php echo $orderId ?? "cart"; ?>',
                        user_id: '<?php echo $userId; ?>',
                        amount: <?php echo $amount; ?>,
                        payment_method: methodId,
                        payment_details: paymentData
                    })
                });

                const createResult = await createResponse.json();
                if (!createResult.success) {
                    throw new Error(createResult.message);
                }

                // Process payment
                const processResponse = await fetch('payment-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'process_payment',
                        payment_id: createResult.payment_id,
                        ...paymentData
                    })
                });

                const processResult = await processResponse.json();
                
                if (processResult.success) {
                    showNotification(processResult.message, 'success');
                    
                    // Clear cart on frontend if payment was successful
                    if (processResult.cart_cleared !== false) {
                        // Update cart count in UI
                        if (typeof updateCartCount === 'function') {
                            updateCartCount(0);
                        }
                        
                        // Update cart count in header if cart notification manager is available
                        if (window.cartNotificationManager) {
                            window.cartNotificationManager.updateCartCount(0);
                        }
                    }
                    
                    setTimeout(() => {
                        window.location.href = 'orders.php?payment_success=true';
                    }, 2000);
                } else {
                    throw new Error(processResult.message);
                }

            } catch (error) {
                showNotification(error.message, 'error');
            } finally {
                loading.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        // Validate form
        function validateForm(methodId) {
            const requiredFields = [];
            
            switch (methodId) {
                case 'waafi':
                    requiredFields.push(
                        { id: `fullname-${methodId}`, name: 'Full Name' },
                        { id: `email-${methodId}`, name: 'Email Address' },
                        { id: `phone-${methodId}`, name: 'Phone Number' },
                        { id: `region-${methodId}`, name: 'Region' },
                        { id: `city-${methodId}`, name: 'City' }
                    );
                    break;
                case 'card':
                    requiredFields.push(
                        { id: `fullname-${methodId}`, name: 'Full Name' },
                        { id: `email-${methodId}`, name: 'Email Address' },
                        { id: 'card-number', name: 'Card Number' },
                        { id: 'card-expiry', name: 'Expiry Date' },
                        { id: 'card-cvv', name: 'CVV' },
                        { id: `region-${methodId}`, name: 'Region' },
                        { id: `city-${methodId}`, name: 'City' }
                    );
                    break;
                case 'bank':
                    requiredFields.push(
                        { id: `fullname-${methodId}`, name: 'Full Name' },
                        { id: `email-${methodId}`, name: 'Email Address' },
                        { id: 'bank-name', name: 'Bank Name' },
                        { id: 'account-number', name: 'Account Number' },
                        { id: 'account-holder', name: 'Account Holder Name' },
                        { id: `region-${methodId}`, name: 'Region' },
                        { id: `city-${methodId}`, name: 'City' }
                    );
                    break;
            }

            for (const field of requiredFields) {
                const element = document.getElementById(field.id);
                if (!element || !element.value.trim()) {
                    showNotification(`Please fill in ${field.name}`, 'error');
                    if (element) element.focus();
                    return false;
                }
            }

            // Validate email format
            const emailField = document.getElementById(`email-${methodId}`);
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    showNotification('Please enter a valid email address', 'error');
                    emailField.focus();
                    return false;
                }
            }

            return true;
        }

        // Get payment data from form
        function getPaymentData(methodId) {
            switch (methodId) {
                case 'waafi':
                    const phone = document.getElementById(`phone-${methodId}`).value;
                    const fullname = document.getElementById(`fullname-${methodId}`).value;
                    const email = document.getElementById(`email-${methodId}`).value;
                    const region = document.getElementById(`region-${methodId}`).value;
                    const city = document.getElementById(`city-${methodId}`).value;
                    const productDescription = document.getElementById(`product-description-${methodId}`).value;
                    
                    if (!phone || !fullname || !email || !region || !city) {
                        showNotification('Please fill all required fields', 'error');
                        return null;
                    }
                    
                    return { 
                        phone_number: '+252' + phone,
                        full_name: fullname,
                        email: email,
                        region: region,
                        city: city,
                        product_description: productDescription
                    };
                
                case 'card':
                    const cardNumber = document.getElementById('card-number').value;
                    const expiry = document.getElementById('card-expiry').value;
                    const cvv = document.getElementById('card-cvv').value;
                    const cardFullname = document.getElementById(`fullname-${methodId}`).value;
                    const cardEmail = document.getElementById(`email-${methodId}`).value;
                    const cardRegion = document.getElementById(`region-${methodId}`).value;
                    const cardCity = document.getElementById(`city-${methodId}`).value;
                    const cardProductDescription = document.getElementById(`product-description-${methodId}`).value;
                    
                    if (!cardNumber || !expiry || !cvv || !cardFullname || !cardEmail || !cardRegion || !cardCity) {
                        showNotification('Please fill all required fields', 'error');
                        return null;
                    }
                    
                    return {
                        card_number: cardNumber,
                        expiry: expiry,
                        cvv: cvv,
                        cardholder_name: cardFullname,
                        full_name: cardFullname,
                        email: cardEmail,
                        region: cardRegion,
                        city: cardCity,
                        product_description: cardProductDescription
                    };
                
                case 'bank':
                    const bankName = document.getElementById('bank-name').value;
                    const accountNumber = document.getElementById('account-number').value;
                    const accountHolder = document.getElementById('account-holder').value;
                    const bankFullname = document.getElementById(`fullname-${methodId}`).value;
                    const bankEmail = document.getElementById(`email-${methodId}`).value;
                    const bankRegion = document.getElementById(`region-${methodId}`).value;
                    const bankCity = document.getElementById(`city-${methodId}`).value;
                    const bankProductDescription = document.getElementById(`product-description-${methodId}`).value;
                    
                    if (!bankName || !accountNumber || !accountHolder || !bankFullname || !bankEmail || !bankRegion || !bankCity) {
                        showNotification('Please fill all required fields', 'error');
                        return null;
                    }
                    
                    return {
                        bank_name: bankName,
                        account_number: accountNumber,
                        account_holder: accountHolder,
                        full_name: bankFullname,
                        email: bankEmail,
                        region: bankRegion,
                        city: bankCity,
                        product_description: bankProductDescription
                    };
                
                default:
                    return {};
            }
        }

        // Update cities based on selected region
        function updateCities(methodId) {
            const regionSelect = document.getElementById(`region-${methodId}`);
            const citySelect = document.getElementById(`city-${methodId}`);
            const selectedRegion = regionSelect.value;
            
            // Clear existing cities
            citySelect.innerHTML = '<option value="">Select City</option>';
            
            if (!selectedRegion) return;
            
            // Define cities for each region
            const cities = {
                banadir: ['Mogadishu', 'Afgooye', 'Marka', 'Wanlaweyn'],
                bari: ['Bosaso', 'Qardho', 'Caluula', 'Iskushuban'],
                bay: ['Baidoa', 'Buurhakaba', 'Dinsor', 'Qansaxdheere'],
                galkacyo: ['Galkacyo', 'Garoowe', 'Burtinle', 'Dangorayo'],
                gedo: ['Garbahaarrey', 'Bardheere', 'Luuq', 'El Wak'],
                hiran: ['Beledweyne', 'Buulobarde', 'Jalalaqsi', 'Mahas'],
                jubaland: ['Kismayo', 'Jamaame', 'Afmadow', 'Badhaadhe'],
                mudug: ['Galkacyo', 'Garoowe', 'Burtinle', 'Dangorayo'],
                nugal: ['Garoowe', 'Eyl', 'Burtinle', 'Dangorayo'],
                sanaag: ['Ceerigaabo', 'Badhan', 'Laasqoray', 'Dhahar'],
                shabeellaha_dhexe: ['Jowhar', 'Balcad', 'Adale', 'Warsheikh'],
                shabeellaha_hoose: ['Marka', 'Afgooye', 'Wanlaweyn', 'Qoryooley'],
                sool: ['Laas Caanood', 'Caynabo', 'Taleex', 'Xudun'],
                togdheer: ['Hargeisa', 'Burao', 'Oodweyne', 'Sheikh'],
                woqooyi_galbeed: ['Hargeisa', 'Berbera', 'Gabiley', 'Burao']
            };
            
            const regionCities = cities[selectedRegion] || [];
            regionCities.forEach(city => {
                const option = document.createElement('option');
                option.value = city.toLowerCase().replace(/\s+/g, '_');
                option.textContent = city;
                citySelect.appendChild(option);
            });
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.classList.add('show');

            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }

        // Format card number
        function formatCardNumber(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            input.value = value;
        }

        // Format expiry date
        function formatExpiry(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            input.value = value;
        }

        // Format phone number (Somali)
        function formatPhoneNumber(input) {
            let value = input.value.replace(/\D/g, '');
            // Limit to 10 digits for Somali numbers (without country code)
            // Full number will be: +252 + 10 digits = 13 characters total
            value = value.substring(0, 10);
            input.value = value;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadPaymentMethods();

            // Add input formatting
            document.addEventListener('input', function(e) {
                if (e.target.id === 'card-number') {
                    formatCardNumber(e.target);
                } else if (e.target.id === 'card-expiry') {
                    formatExpiry(e.target);
                } else if (e.target.id.includes('phone-')) {
                    formatPhoneNumber(e.target);
                }
            });
        });
    </script>
</body>
</html>
