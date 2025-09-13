<?php
/**
 * Place Order Page
 * Handles the checkout process and order placement
 */

session_start();
require_once 'config1/mongodb.php';
require_once 'models/Cart.php';
require_once 'models/Order.php';
require_once 'models/Payment.php';

// Include cart configuration for consistent user ID
if (file_exists('cart-config.php')) {
    require_once 'cart-config.php';
}

// Get user ID from session or use consistent default
$userId = $_SESSION['user_id'] ?? $_SESSION['current_cart_user_id'] ?? 'main_user_default';

    $cartModel = new Cart();
$orderModel = new Order();

// Get current cart
    $cart = $cartModel->getCart($userId);

// Check if cart is empty
if (empty($cart['items'])) {
    header('Location: cart-unified.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order - Glamour</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header h1 {
            color: #0066cc;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: #f8f9fa;
            color: #0066cc;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #0066cc;
            color: white;
            border-color: #0066cc;
        }

        .content-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
            margin-bottom: 20px;
        }

        .checkout-form {
            display: grid;
            gap: 20px;
        }

        .form-group {
            display: grid;
            gap: 8px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0066cc;
        }

        .payment-methods {
            display: grid;
            gap: 15px;
            margin: 20px 0;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: #0066cc;
            background: #f8f9fa;
        }

        .payment-method input[type="radio"] {
            margin: 0;
        }

        .payment-method.selected {
            border-color: #0066cc;
            background: #e6f3ff;
        }

        .cart-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
            text-align: center;
        }

        .cart-summary h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 1.2rem;
            color: #0066cc;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-primary {
            background: #0066cc;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .modal h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .modal p {
            color: #666;
            margin-bottom: 20px;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-credit-card"></i>
                Place Order
            </h1>
            <a href="cart-unified.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Cart
        </a>
        </div>

        <!-- Checkout Form -->
        <div class="content-section">
            <form id="checkoutForm" class="checkout-form">
                            <div class="form-group">
                    <label for="shipping_address">Shipping Address *</label>
                    <textarea id="shipping_address" name="shipping_address" rows="3" required 
                              placeholder="Enter your complete shipping address"></textarea>
                        </div>
                        
                            <div class="form-group">
                    <label for="billing_address">Billing Address</label>
                    <textarea id="billing_address" name="billing_address" rows="3" 
                                          placeholder="Enter your billing address (optional)"></textarea>
                        </div>
                        
                            <div class="form-group">
                    <label for="payment_method">Payment Method *</label>
                    <div class="payment-methods">
                        <div class="payment-method" onclick="selectPaymentMethod('cash_on_delivery')">
                            <input type="radio" id="cash_on_delivery" name="payment_method" value="cash_on_delivery" checked>
                            <label for="cash_on_delivery">Cash on Delivery</label>
                                    </div>
                        <div class="payment-method" onclick="selectPaymentMethod('waafi')">
                            <input type="radio" id="waafi" name="payment_method" value="waafi">
                            <label for="waafi">Waafi</label>
                                </div>
                        <div class="payment-method" onclick="selectPaymentMethod('sahal')">
                            <input type="radio" id="sahal" name="payment_method" value="sahal">
                            <label for="sahal">SAHAL</label>
                                </div>
                        <div class="payment-method" onclick="selectPaymentMethod('saad')">
                            <input type="radio" id="saad" name="payment_method" value="saad">
                            <label for="saad">SAAD</label>
                            </div>
                        <div class="payment-method" onclick="selectPaymentMethod('evc')">
                            <input type="radio" id="evc" name="payment_method" value="evc">
                            <label for="evc">EVC</label>
                        </div>
                        <div class="payment-method" onclick="selectPaymentMethod('edahab')">
                            <input type="radio" id="edahab" name="payment_method" value="edahab">
                            <label for="edahab">EDAHAB</label>
                                </div>
                            </div>
                        </div>
                        
                            <div class="form-group">
                    <label for="notes">Order Notes</label>
                    <textarea id="notes" name="notes" rows="3" 
                              placeholder="Any special instructions for your order"></textarea>
                        </div>
                        
                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Items (<?php echo $cart['item_count']; ?>):</span>
                        <span>$<?php echo number_format($cart['total'], 2); ?></span>
                            </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($cart['total'], 2); ?></span>
                            </div>
                        </div>
                        
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i>
                        Place Order
                    </button>
                    <a href="cart-unified.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Cart
                    </a>
                            </div>
            </form>
                            </div>
                        </div>
                        
    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Confirm Action</h3>
            <p id="modalMessage">Are you sure you want to proceed?</p>
            <div class="modal-buttons">
                <button onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                <button onclick="confirmAction()" class="btn btn-primary" id="confirmBtn">Confirm</button>
                            </div>
                            </div>
                        </div>
                        
    <script>
        let currentAction = null;
        let currentCallback = null;

        // Modal functions
        function showModal(title, message, callback) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('modal').style.display = 'block';
            currentCallback = callback;
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
            currentCallback = null;
        }

        function confirmAction() {
            if (currentCallback) {
                currentCallback();
            }
            closeModal();
        }

        // Checkout functions
        function selectPaymentMethod(method) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selected class to clicked payment method
            event.currentTarget.classList.add('selected');
            
            // Check the radio button
            document.getElementById(method).checked = true;
        }

        // Checkout form submission
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'place_order');
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            fetch('cart-api.php', {
                    method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showModal('Success', 'Order placed successfully! Your order ID is: ' + data.order_id, function() {
                        closeModal();
                        window.location.href = 'orders.php?order_id=' + data.order_id;
                    });
                } else {
                    showModal('Error', 'Error placing order: ' + data.message, closeModal);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                showModal('Error', 'Error placing order: ' + error.message, closeModal);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    </script>
</body>
</html>


