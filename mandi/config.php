<?php
// ============================================
// TOMATO MANDIMART - CONFIGURATION
// ============================================

session_start();

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Change for production
define('DB_PASS', '');              // Change for production
define('DB_NAME', 'tomato_mandimart');

// Application Settings
define('SITE_NAME', 'Tomato MandiMart');
define('SITE_URL', 'http://localhost/tomato-mandimart');  // Change for production
define('ADMIN_EMAIL', 'admin@tomatomart.com');

// Security
define('SESSION_TIMEOUT', 3600); // 1 hour

// File Upload Settings
define('UPLOAD_DIR', __DIR__ . '/assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Kolkata');
?>

