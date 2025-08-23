<?php
/**
 * Glamour Shopping System - Main Entry Point
 * Redirects users to appropriate sections based on their needs
 */

session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdminLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Get the requested section from URL parameters
$section = $_GET['section'] ?? '';

// Redirect based on section or default to main page
switch ($section) {
    case 'women':
        header('Location: womenF/index.php');
        exit;
    case 'men':
        header('Location: menfolder/men.php');
        exit;
    case 'perfumes':
        header('Location: perfumes/index.php');
        exit;
    case 'shoes':
        header('Location: shoess/men.php');
        exit;
    case 'home-decor':
        header('Location: home-decor/homedecor.php');
        exit;
    case 'admin':
        if ($isAdminLoggedIn) {
            header('Location: admin/index.php');
        } else {
            header('Location: admin/login.php');
        }
        exit;
    case 'cart':
        header('Location: cart.php');
        exit;
    case 'products':
        header('Location: index.php');
        exit;
    default:
        // Default to the main HTML page
        include 'index.html';
        exit;
}
?>
