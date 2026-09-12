<?php
require_once 'includes/config.php';

// Clear all session data
$_SESSION = array();

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();

redirect('index.php', 'You have been logged out successfully.', 'success');
?>

