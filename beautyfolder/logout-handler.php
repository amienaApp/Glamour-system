<?php
// Beauty & Cosmetics Logout Handler
// Simple logout handler for beauty section

session_start();

// Clear all session data
session_unset();
session_destroy();

// Redirect to beauty page
header('Location: beauty.php');
exit();
?>





