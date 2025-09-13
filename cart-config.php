<?php
// Cart Configuration
// This file ensures consistent user ID across all cart operations
// Only works with authenticated users - no default users allowed

// Function to get user ID (returns null if not authenticated)
function getCartUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Function to check if user is authenticated
function isUserAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}
?>
