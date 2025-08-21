<?php
/**
 * Payment System Demo
 * Demonstrates the payment functionality with a simple interface
 */

require_once 'models/Payment.php';
require_once 'models/Order.php';
require_once 'models/Cart.php';

$userId = 'demo_user_123';
$paymentModel = new Payment();
$orderModel = new Order();

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $paymentMethod = $_POST['payment_method'] ?? '';
        $amount = floatval($_POST['amount'] ?? 0);
        $phoneNumber = $_POST['phone_number'] ?? '';
        $cardNumber = $_POST['card_number'] ?? '';
        $bankName = $_POST['bank_name'] ?? '';
        
        if ($amount <= 0) {
            throw new Exception('Please enter a valid amount');
        }
        
        // Create a test order ID
        $orderId = 'demo_order_' . time();
        
        // Create payment
        $paymentData = [
            'order_id' => $orderId,
            'user_id' => $userId,
            'amount' => $amount,
            'currency' => 'USD',
            'payment_method' => $paymentMethod,
            'payment_details' => []
        ];
        
        $paymentId = $paymentModel->createPayment($paymentData);
        
        if (!$paymentId) {
            throw new Exception('Failed to create payment');
        }
        
        // Process payment based on method
        $processData = [];
        switch ($paymentMethod) {
            case 'waafi':
                if (empty($phoneNumber)) {
                    throw new Exception('Phone number is required for Waafi payment');
                }
                $processData['phone_number'] = $phoneNumber;
                break;
            case 'card':
                if (empty($cardNumber)) {
                    throw new Exception('Card number is required for card payment');
                }
                $processData['card_number'] = $cardNumber;
                $processData['expiry'] = $_POST['card_expiry'] ?? '';
                $processData['cvv'] = $_POST['card_cvv'] ?? '';
                break;
            case 'bank':
                if (empty($bankName)) {
                    throw new Exception('Bank name is required for bank transfer');
                }
                $processData['bank_name'] = $bankName;
                $processData['account_number'] = $_POST['account_number'] ?? '';
                $processData['account_holder'] = $_POST['account_holder'] ?? '';
                break;
        }
        
        $result = $paymentModel->processPayment($paymentId, $processData);
        
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
        } else {
            throw new Exception($result['message']);
        }
        
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Get payment statistics
$stats = $paymentModel->getPaymentStatistics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment System Demo - Glamour System</title>
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
            max-width: 1000px;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #0066cc;
        }

        .stat-label {
            color: #666;
            font-size: 1rem;
            font-weight: 500;
        }

        .demo-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .payment-form {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
        }

        .form-title {
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

        .payment-methods {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .method-btn {
            flex: 1;
            padding: 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            font-weight: 500;
        }

        .method-btn:hover {
            border-color: #0066cc;
            background: #f8fbff;
        }

        .method-btn.active {
            border-color: #0066cc;
            background: #0066cc;
            color: white;
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

        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
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

        .payment-details {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e1e8ed;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: #333;
        }

        .detail-value {
            color: #0066cc;
            font-weight: 600;
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

        @media (max-width: 768px) {
            .demo-container {
                grid-template-columns: 1fr;
            }
            
            .payment-methods {
                flex-direction: column;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>

        <div class="header">
            <h1>Payment System Demo</h1>
            <p>Test the Somali payment methods functionality</p>
        </div>

        <!-- Payment Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_payments']; ?></div>
                <div class="stat-label">Total Payments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['completed_payments']; ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['pending_payments']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['failed_payments']; ?></div>
                <div class="stat-label">Failed</div>
            </div>
        </div>

        <div class="demo-container">
            <!-- Payment Form -->
            <div class="payment-form">
                <h3 class="form-title">
                    <i class="fas fa-credit-card"></i>
                    Test Payment
                </h3>

                <?php if ($message): ?>
                    <div class="message <?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="amount">Amount (USD)</label>
                        <input type="number" id="amount" name="amount" class="form-input" 
                               placeholder="Enter amount" step="0.01" min="0.01" required>
                    </div>

                    <div class="form-group">
                        <label>Payment Method</label>
                        <div class="payment-methods">
                            <button type="button" class="method-btn active" data-method="waafi">
                                <i class="fas fa-mobile-alt"></i><br>
                                Waafi
                            </button>
                            <button type="button" class="method-btn" data-method="card">
                                <i class="fas fa-credit-card"></i><br>
                                Card
                            </button>
                            <button type="button" class="method-btn" data-method="bank">
                                <i class="fas fa-university"></i><br>
                                Bank
                            </button>
                        </div>
                        <input type="hidden" id="payment_method" name="payment_method" value="waafi">
                    </div>

                    <!-- Waafi Fields -->
                    <div id="waafi-fields" class="method-fields">
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" id="phone_number" name="phone_number" class="form-input" 
                                   placeholder="+2520901234567">
                            <small style="color: #666; font-size: 0.8rem;">
                                Supports: SAHAL (090), SAAD (063), EVC (061), EDAHAB (066)
                            </small>
                        </div>
                    </div>

                    <!-- Card Fields -->
                    <div id="card-fields" class="method-fields" style="display: none;">
                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <input type="text" id="card_number" name="card_number" class="form-input" 
                                   placeholder="1234 5678 9012 3456">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label for="card_expiry">Expiry Date</label>
                                <input type="text" id="card_expiry" name="card_expiry" class="form-input" 
                                       placeholder="MM/YY">
                            </div>
                            <div class="form-group">
                                <label for="card_cvv">CVV</label>
                                <input type="text" id="card_cvv" name="card_cvv" class="form-input" 
                                       placeholder="123">
                            </div>
                        </div>
                    </div>

                    <!-- Bank Fields -->
                    <div id="bank-fields" class="method-fields" style="display: none;">
                        <div class="form-group">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" id="bank_name" name="bank_name" class="form-input" 
                                   placeholder="Enter bank name">
                        </div>
                        <div class="form-group">
                            <label for="account_number">Account Number</label>
                            <input type="text" id="account_number" name="account_number" class="form-input" 
                                   placeholder="Enter account number">
                        </div>
                        <div class="form-group">
                            <label for="account_holder">Account Holder Name</label>
                            <input type="text" id="account_holder" name="account_holder" class="form-input" 
                                   placeholder="Enter account holder name">
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-lock"></i>
                        Process Payment
                    </button>
                </form>
            </div>

            <!-- Payment Details -->
            <div class="payment-details">
                <h3 class="form-title">
                    <i class="fas fa-info-circle"></i>
                    Payment Information
                </h3>

                <div class="detail-item">
                    <span class="detail-label">Supported Methods:</span>
                    <span class="detail-value">3</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Waafi (Mobile Money):</span>
                    <span class="detail-value">SAHAL, SAAD, EVC, EDAHAB</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Credit/Debit Cards:</span>
                    <span class="detail-value">Visa, Mastercard, Amex</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Bank Transfer:</span>
                    <span class="detail-value">Direct Transfer</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Currency:</span>
                    <span class="detail-value">USD</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Security:</span>
                    <span class="detail-value">SSL Encrypted</span>
                </div>

                <div style="margin-top: 30px; padding: 20px; background: #f8fbff; border-radius: 10px;">
                    <h4 style="color: #0066cc; margin-bottom: 10px;">
                        <i class="fas fa-shield-alt"></i>
                        Security Features
                    </h4>
                    <ul style="color: #666; line-height: 1.6;">
                        <li>Phone number validation for Somali numbers</li>
                        <li>Card number format validation</li>
                        <li>Secure payment processing</li>
                        <li>Transaction ID generation</li>
                        <li>Payment status tracking</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle payment method selection
        document.querySelectorAll('.method-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Update hidden input
                const method = this.getAttribute('data-method');
                document.getElementById('payment_method').value = method;
                
                // Show/hide method fields
                document.querySelectorAll('.method-fields').forEach(field => {
                    field.style.display = 'none';
                });
                
                document.getElementById(method + '-fields').style.display = 'block';
            });
        });

        // Format phone number input
        document.getElementById('phone_number').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 0 && !value.startsWith('252')) {
                if (value.startsWith('0')) {
                    value = '252' + value;
                } else {
                    value = '252' + value;
                }
            }
            this.value = value;
        });

        // Format card number input
        document.getElementById('card_number').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            this.value = value;
        });

        // Format expiry date input
        document.getElementById('card_expiry').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value;
        });
    </script>
</body>
</html>
