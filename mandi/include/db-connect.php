<?php
// ============================================
// DATABASE CONNECTION
// ============================================
require_once __DIR__ . '/../config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to handle Hindi and special characters
$conn->set_charset("utf8mb4");
?>

