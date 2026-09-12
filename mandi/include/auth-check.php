<?php
require_once __DIR__ . '/functions.php';

if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    redirect('login.php', 'Please login to access this page.', 'warning');
}
?>

