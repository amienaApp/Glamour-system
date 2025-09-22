<?php
/**
 * Set Payment Success Session Variable
 * This file sets a session variable to indicate payment success
 * without showing it in the URL
 */

session_start();

// Set payment success flag in session
if (isset($_POST['payment_success']) && $_POST['payment_success'] === 'true') {
    $_SESSION['payment_success'] = true;
    $_SESSION['payment_success_time'] = time();
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Payment success flag set']);
} else {
    // Return error response
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>




