<?php
// Cart Configuration
// This file ensures consistent user ID across all cart operations

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'main_user_1756062003';
}

if (!isset($_SESSION['current_cart_user_id'])) {
    $_SESSION['current_cart_user_id'] = 'main_user_1756062003';
}

// Function to get consistent user ID
function getCartUserId() {
    return $_SESSION['user_id'] ?? 'main_user_1756062003';
}
?>