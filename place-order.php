<?php
// Suppress warnings and errors
error_reporting(0);
ini_set('display_errors', 0);

/**
 * Payment Page
 * Displays payment methods and forms for Somali payment options
 */

require_once 'models/Payment.php';
require_once 'models/Order.php';
require_once 'models/Cart.php';

// Get order details if order_id is provided
$orderId = $_GET['order_id'] ?? null;
session_start();
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'demo_user_123';

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

$amount = $order ? $order['total'] : ($cart ? $cart['total'] : 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order - Glamour System</title>
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

        .order-form-container {
            margin-bottom: 30px;
        }

        .payment-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .section-title {
            color: #0066cc;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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

        .payment-methods {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-bottom: 20px;
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
        <a href="cart.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Cart
        </a>

        <div class="header">
            <h1>Place Order</h1>
            <p>Complete your order details and payment information</p>
        </div>

        <div class="amount-display">
            <h2>$<?php echo number_format($amount, 2); ?></h2>
            <p>Total Amount to Pay</p>
        </div>

        <div class="order-form-container">
            <!-- Payment Section -->
            <div class="payment-section">
                <h3 class="section-title">
                    <i class="fas fa-credit-card"></i>
                    Order & Payment Information
                </h3>
                
                <div class="payment-methods" id="paymentMethods">
                    <!-- Payment methods will be loaded here -->
                </div>
                
                <div class="payment-form" id="paymentForm">
                    <!-- Payment form will be generated here -->
                </div>
                
                <button class="submit-btn" id="placeOrderBtn" onclick="placeOrder()">
                    <i class="fas fa-shopping-cart"></i>
                    Place Order - $<?php echo number_format($amount, 2); ?>
                </button>
            </div>
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
                console.log('Loading payment methods...');
                const response = await fetch('payment-api.php?t=' + Date.now(), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'get_payment_methods'
                    })
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                const responseText = await response.text();
                console.log('Raw response:', responseText);
                
                const data = JSON.parse(responseText);
                console.log('Parsed data:', data);
                
                if (data.success) {
                    paymentMethods = data.methods;
                    renderPaymentMethods();
                }
            } catch (error) {
                console.error('Error loading payment methods:', error);
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
                                <label for="phone-${method.id}" class="required-field">Phone Number</label>
                                <div class="phone-input-group">
                                    <div class="country-code">
                                        <div class="flag-icon"></div>
                                        +252
                                    </div>
                                    <input type="tel" id="phone-${method.id}" class="form-input phone-input" 
                                           placeholder="61 xxx xxxx" maxlength="9" required>
                                </div>
                                <div class="help-text">
                                    Enter your Somali phone number (9 digits)
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4><i class="fas fa-map-marker-alt"></i> Address Information</h4>
                            <div class="form-group">
                                <label for="shipping-${method.id}" class="required-field">Shipping Address</label>
                                <textarea id="shipping-${method.id}" class="form-input" 
                                          placeholder="Enter your complete shipping address" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="billing-${method.id}">Billing Address</label>
                                <textarea id="billing-${method.id}" class="form-input" 
                                          placeholder="Enter your billing address (optional)"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4><i class="fas fa-mobile-alt"></i> Mobile Money Details</h4>
                            <div class="form-group">
                                <label for="waafiPhone" class="required-field">Mobile Money Phone Number</label>
                                <div class="phone-input-group">
                                    <div class="country-code">
                                        <div class="flag-icon"></div>
                                        +252
                                    </div>
                                    <input type="tel" id="waafiPhone" class="form-input phone-input" 
                                           placeholder="090 xxx xxxx" maxlength="10" required>
                                </div>
                                <div class="help-text">
                                    Supports: SAHAL (090), SAAD (063), EVC (061), EDAHAB (066) - Enter 10 digits
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4><i class="fas fa-sticky-note"></i> Order Notes</h4>
                            <div class="form-group">
                                <label for="notes-${method.id}">Special Instructions</label>
                                <textarea id="notes-${method.id}" class="form-input" 
                                          placeholder="Any special instructions or notes for your order"></textarea>
                            </div>
                        </div>
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
                                <label for="phone-${method.id}" class="required-field">Phone Number</label>
                                <div class="phone-input-group">
                                    <div class="country-code">
                                        <div class="flag-icon"></div>
                                        +252
                                    </div>
                                    <input type="tel" id="phone-${method.id}" class="form-input phone-input" 
                                           placeholder="61 xxx xxxx" maxlength="9" required>
                                </div>
                                <div class="help-text">
                                    Enter your Somali phone number (9 digits)
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4><i class="fas fa-map-marker-alt"></i> Address Information</h4>
                            <div class="form-group">
                                <label for="shipping-${method.id}" class="required-field">Shipping Address</label>
                                <textarea id="shipping-${method.id}" class="form-input" 
                                          placeholder="Enter your complete shipping address" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="billing-${method.id}">Billing Address</label>
                                <textarea id="billing-${method.id}" class="form-input" 
                                          placeholder="Enter your billing address (optional)"></textarea>
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
                        
                        <div class="form-section">
                            <h4><i class="fas fa-sticky-note"></i> Order Notes</h4>
                            <div class="form-group">
                                <label for="notes-${method.id}">Special Instructions</label>
                                <textarea id="notes-${method.id}" class="form-input" 
                                          placeholder="Any special instructions or notes for your order"></textarea>
                            </div>
                        </div>
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
                                <label for="phone-${method.id}" class="required-field">Phone Number</label>
                                <div class="phone-input-group">
                                    <div class="country-code">
                                        <div class="flag-icon"></div>
                                        +252
                                    </div>
                                    <input type="tel" id="phone-${method.id}" class="form-input phone-input" 
                                           placeholder="61 xxx xxxx" maxlength="9" required>
                                </div>
                                <div class="help-text">
                                    Enter your Somali phone number (9 digits)
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4><i class="fas fa-map-marker-alt"></i> Address Information</h4>
                            <div class="form-group">
                                <label for="shipping-${method.id}" class="required-field">Shipping Address</label>
                                <textarea id="shipping-${method.id}" class="form-input" 
                                          placeholder="Enter your complete shipping address" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="billing-${method.id}">Billing Address</label>
                                <textarea id="billing-${method.id}" class="form-input" 
                                          placeholder="Enter your billing address (optional)"></textarea>
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
                        
                        <div class="form-section">
                            <h4><i class="fas fa-sticky-note"></i> Order Notes</h4>
                            <div class="form-group">
                                <label for="notes-${method.id}">Special Instructions</label>
                                <textarea id="notes-${method.id}" class="form-input" 
                                          placeholder="Any special instructions or notes for your order"></textarea>
                            </div>
                        </div>
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
                    
                    // Add event listeners to phone inputs in the activated form
                    setTimeout(() => {
                        addPhoneInputListeners(methodId);
                    }, 100);
                }
            }

            selectedMethod = methodId;
        }

        // Place order
        async function placeOrder() {
            const loading = document.getElementById('loading');
            const placeOrderBtn = document.getElementById('placeOrderBtn');
            const originalText = placeOrderBtn.innerHTML;

            try {
                console.log('Starting place order process...');
                
                // Validate order details
                if (!validateOrderDetails()) {
                    console.log('Order validation failed');
                    return;
                }

                // Validate payment details
                if (!validatePaymentDetails()) {
                    console.log('Payment validation failed');
                    return;
                }

                loading.style.display = 'block';
                placeOrderBtn.disabled = true;
                placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

                // Get order data
                const orderData = getOrderData();
                const paymentData = getPaymentData();

                // Create order first
                const orderResponse = await fetch('cart-api.php?t=' + Date.now(), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'place_order',
                        user_id: '<?php echo $userId; ?>',
                        ...orderData
                    })
                });

                const orderResultText = await orderResponse.text();
                console.log('Order response text:', orderResultText);
                
                // Check if response is HTML instead of JSON
                if (orderResultText.trim().startsWith('<')) {
                    console.error('Received HTML instead of JSON:', orderResultText);
                    throw new Error('Server returned HTML instead of JSON. Please check the console for details.');
                }
                
                let orderResult;
                try {
                    orderResult = JSON.parse(orderResultText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response text:', orderResultText);
                    throw new Error('Invalid JSON response from server. Please check the console for details.');
                }
                if (!orderResult.success) {
                    throw new Error(orderResult.message);
                }

                // Create payment
                const createResponse = await fetch('payment-api.php?t=' + Date.now(), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'create_payment',
                        order_id: orderResult.order_id,
                        user_id: '<?php echo $userId; ?>',
                        amount: <?php echo $amount; ?>,
                        payment_method: selectedMethod,
                        payment_details: paymentData
                    })
                });

                const createResultText = await createResponse.text();
                console.log('Create payment response text:', createResultText);
                
                // Check if response is HTML instead of JSON
                if (createResultText.trim().startsWith('<')) {
                    console.error('Received HTML instead of JSON:', createResultText);
                    throw new Error('Server returned HTML instead of JSON. Please check the console for details.');
                }
                
                let createResult;
                try {
                    createResult = JSON.parse(createResultText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response text:', createResultText);
                    throw new Error('Invalid JSON response from server. Please check the console for details.');
                }
                if (!createResult.success) {
                    throw new Error(createResult.message);
                }

                // Process payment
                const processResponse = await fetch('payment-api.php?t=' + Date.now(), {
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

                const processResultText = await processResponse.text();
                console.log('Process payment response text:', processResultText);
                
                // Check if response is HTML instead of JSON
                if (processResultText.trim().startsWith('<')) {
                    console.error('Received HTML instead of JSON:', processResultText);
                    throw new Error('Server returned HTML instead of JSON. Please check the console for details.');
                }
                
                let processResult;
                try {
                    processResult = JSON.parse(processResultText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response text:', processResultText);
                    throw new Error('Invalid JSON response from server. Please check the console for details.');
                }
                
                if (processResult.success) {
                    showNotification('Order placed successfully! Check your email for confirmation.', 'success');
                    setTimeout(() => {
                        window.location.href = 'orders.php?payment_success=true';
                    }, 3000);
                } else {
                    throw new Error(processResult.message);
                }

            } catch (error) {
                showNotification(error.message, 'error');
            } finally {
                loading.style.display = 'none';
                placeOrderBtn.disabled = false;
                placeOrderBtn.innerHTML = originalText;
            }
        }

        // Validate order details
        function validateOrderDetails() {
            if (!selectedMethod) {
                showNotification('Please select a payment method', 'error');
                return false;
            }

            const requiredFields = [
                { id: `fullname-${selectedMethod}`, name: 'Full Name' },
                { id: `email-${selectedMethod}`, name: 'Email Address' },
                { id: `phone-${selectedMethod}`, name: 'Phone Number' },
                { id: `shipping-${selectedMethod}`, name: 'Shipping Address' }
            ];

            for (const field of requiredFields) {
                const element = document.getElementById(field.id);
                if (!element || !element.value.trim()) {
                    showNotification(`Please fill in ${field.name}`, 'error');
                    if (element) element.focus();
                    return false;
                }
            }

            // Validate email format
            const emailField = document.getElementById(`email-${selectedMethod}`);
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

        // Validate payment details
        function validatePaymentDetails() {
            switch (selectedMethod) {
                case 'waafi':
                    const phone = document.getElementById('waafiPhone');
                    if (!phone || !phone.value.trim()) {
                        showNotification('Please enter your phone number', 'error');
                        if (phone) phone.focus();
                        return false;
                    }
                    break;
                
                case 'card':
                    const cardNumber = document.getElementById('card-number');
                    const cardExpiry = document.getElementById('card-expiry');
                    const cardCvv = document.getElementById('card-cvv');
                    
                    if (!cardNumber || !cardNumber.value.trim()) {
                        showNotification('Please enter card number', 'error');
                        if (cardNumber) cardNumber.focus();
                        return false;
                    }
                    if (!cardExpiry || !cardExpiry.value.trim()) {
                        showNotification('Please enter expiry date', 'error');
                        if (cardExpiry) cardExpiry.focus();
                        return false;
                    }
                    if (!cardCvv || !cardCvv.value.trim()) {
                        showNotification('Please enter CVV', 'error');
                        if (cardCvv) cardCvv.focus();
                        return false;
                    }
                    break;
                
                case 'bank':
                    const bankName = document.getElementById('bank-name');
                    const accountNumber = document.getElementById('account-number');
                    const accountHolder = document.getElementById('account-holder');
                    
                    if (!bankName || !bankName.value.trim()) {
                        showNotification('Please enter bank name', 'error');
                        if (bankName) bankName.focus();
                        return false;
                    }
                    if (!accountNumber || !accountNumber.value.trim()) {
                        showNotification('Please enter account number', 'error');
                        if (accountNumber) accountNumber.focus();
                        return false;
                    }
                    if (!accountHolder || !accountHolder.value.trim()) {
                        showNotification('Please enter account holder name', 'error');
                        if (accountHolder) accountHolder.focus();
                        return false;
                    }
                    break;
            }

            return true;
        }

        // Get order data
        function getOrderData() {
            const billingAddress = document.getElementById(`billing-${selectedMethod}`).value || 
                                  document.getElementById(`shipping-${selectedMethod}`).value;
            
            return {
                full_name: document.getElementById(`fullname-${selectedMethod}`).value,
                email: document.getElementById(`email-${selectedMethod}`).value,
                phone: document.getElementById(`phone-${selectedMethod}`).value,
                shipping_address: document.getElementById(`shipping-${selectedMethod}`).value,
                billing_address: billingAddress,
                order_notes: document.getElementById(`notes-${selectedMethod}`).value,
                payment_method: selectedMethod
            };
        }

        // Validate form
        function validateForm(methodId) {
            const requiredFields = [];
            
            switch (methodId) {
                case 'waafi':
                    requiredFields.push(
                        { id: `fullname-${methodId}`, name: 'Full Name' },
                        { id: `email-${methodId}`, name: 'Email Address' },
                        { id: `phone-${methodId}`, name: 'Phone Number' }
                    );
                    break;
                case 'card':
                    requiredFields.push(
                        { id: `fullname-${methodId}`, name: 'Full Name' },
                        { id: `email-${methodId}`, name: 'Email Address' },
                        { id: 'card-number', name: 'Card Number' },
                        { id: 'card-expiry', name: 'Expiry Date' },
                        { id: 'card-cvv', name: 'CVV' }
                    );
                    break;
                case 'bank':
                    requiredFields.push(
                        { id: `fullname-${methodId}`, name: 'Full Name' },
                        { id: `email-${methodId}`, name: 'Email Address' },
                        { id: 'bank-name', name: 'Bank Name' },
                        { id: 'account-number', name: 'Account Number' },
                        { id: 'account-holder', name: 'Account Holder Name' }
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
        function getPaymentData() {
            const baseData = {
                full_name: document.getElementById(`fullname-${selectedMethod}`).value,
                email: document.getElementById(`email-${selectedMethod}`).value
            };

            switch (selectedMethod) {
                case 'waafi':
                    return {
                        ...baseData,
                        phone_number: '+252' + document.getElementById('waafiPhone').value
                    };
                
                case 'card':
                    return {
                        ...baseData,
                        card_number: document.getElementById('card-number').value,
                        expiry: document.getElementById('card-expiry').value,
                        cvv: document.getElementById('card-cvv').value,
                        cardholder_name: document.getElementById(`fullname-${selectedMethod}`).value
                    };
                
                case 'bank':
                    return {
                        ...baseData,
                        bank_name: document.getElementById('bank-name').value,
                        account_number: document.getElementById('account-number').value,
                        account_holder: document.getElementById('account-holder').value
                    };
                
                default:
                    return baseData;
            }
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
            
            // Check if this is a Waafi phone input (10 digits) or regular phone input (9 digits)
            if (input.id === 'waafiPhone') {
                // Waafi mobile money numbers (10 digits)
                value = value.substring(0, 10);
            } else {
                // Regular phone numbers (9 digits)
                value = value.substring(0, 9);
            }
            
            input.value = value;
        }
        
        // Add event listeners to phone inputs in the activated form
        function addPhoneInputListeners(methodId) {
            // Add listener for regular phone input
            const phoneInput = document.getElementById(`phone-${methodId}`);
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    formatPhoneNumber(this);
                });
            }
            
            // Add listener for Waafi phone input (only for Waafi payment method)
            if (methodId === 'waafi') {
                const waafiPhoneInput = document.getElementById('waafiPhone');
                if (waafiPhoneInput) {
                    waafiPhoneInput.addEventListener('input', function() {
                        formatPhoneNumber(this);
                    });
                }
            }
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
