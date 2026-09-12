<?php
// ============================================
// HELPER FUNCTIONS
// ============================================
require_once __DIR__ . '/db-connect.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check user type
function isFarmer() {
    return isLoggedIn() && $_SESSION['user_type'] === 'farmer';
}

function isBuyer() {
    return isLoggedIn() && $_SESSION['user_type'] === 'buyer';
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user_type'] === 'admin';
}

// Redirect with message
function redirect($url, $message = '', $type = 'info') {
    if (!empty($message)) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: " . $url);
    exit();
}

// Show flash message
function showFlash() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'info';
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ][$type] ?? 'alert-info';
        
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($_SESSION['flash_message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

// Sanitize input
function clean($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $conn->real_escape_string($data);
}

// Format price in Indian format
function formatPrice($amount) {
    return '₹' . number_format($amount, 0);
}

// Format date
function formatDate($date) {
    return date('d M Y, h:i A', strtotime($date));
}

// Get time remaining
function timeRemaining($endTime) {
    $diff = strtotime($endTime) - time();
    if ($diff <= 0) return 'Ended';
    
    $hours = floor($diff / 3600);
    $mins = floor(($diff % 3600) / 60);
    $secs = $diff % 60;
    
    return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
}

// Upload image
function uploadImage($file, $prefix = '') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    if (!in_array($file['type'], ALLOWED_TYPES)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF allowed.'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'File too large. Max 5MB.'];
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $filepath = UPLOAD_DIR . $filename;
    
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => 'assets/uploads/' . $filename];
    }
    
    return ['success' => false, 'error' => 'Upload failed'];
}

// Get variety emoji
function varietyEmoji($variety) {
    if (stripos($variety, 'cherry') !== false) return '🍒';
    if (stripos($variety, 'roma') !== false || stripos($variety, 'plum') !== false) return '🟠';
    if (stripos($variety, 'hybrid') !== false) return '🍅';
    return '🍅';
}

// Chatbot response
function chatbotReply($question) {
    $q = strtolower(trim($question));
    
    if (strpos($q, 'price') !== false || strpos($q, 'rate') !== false || strpos($q, 'mandi') !== false)
        return '📊 Today\'s Tomato prices: Desi ₹1,500/q, Hybrid ₹2,400/q, Cherry ₹3,750/q at Azadpur Mandi. Check Mandi Prices page for live rates!';
    
    if (strpos($q, 'auction') !== false)
        return '🔨 Go to Auctions page. Farmers create tomato auctions, buyers bid +5% each time, highest bidder wins when time runs out!';
    
    if (strpos($q, 'sell') !== false || strpos($q, 'list') !== false)
        return '🍅 Farmers can list tomatoes from their dashboard. Add variety, quantity, grade, and expected price. Buyers contact directly!';
    
    if (strpos($q, 'buy') !== false)
        return '🛒 Browse crop listings or join live auctions. Contact farmers directly - no middlemen, better prices for everyone!';
    
    if (strpos($q, 'quality') !== false || strpos($q, 'compare') !== false || strpos($q, 'ai') !== false)
        return '🧪 Use AI Quality Check! Upload tomato images, our system analyzes freshness, color, defects, and gives grade recommendation.';
    
    if (strpos($q, 'variety') !== false || strpos($q, 'type') !== false)
        return '🍅 We have 4 varieties: Desi/Local (tangy, cooking), Hybrid TO-1057 (high yield), Cherry (sweet, salad), Roma/Plum (paste, sauce).';
    
    if (strpos($q, 'tip') !== false || strpos($q, 'farm') !== false || strpos($q, 'grow') !== false)
        return '🌱 Tomato Tip: Maintain 21-24°C, 60-70% humidity. Use drip irrigation. Harvest at 90% red for best price. Store at 12-15°C.';
    
    if (strpos($q, 'hello') !== false || strpos($q, 'hi') !== false || strpos($q, 'namaste') !== false)
        return '🙏 Namaste! I am TomatoBot. Ask me about prices, mandis, auctions, selling, buying, or farming tips!';
    
    if (strpos($q, 'hindi') !== false || strpos($q, 'हिंदी') !== false)
        return 'हाँ! मैं हिंदी में भी जवाब दे सकता हूँ। टमाटर की कीमत, मंडी, नीलामी, या खेती के बारे में पूछें।';
    
    return '🤖 I can help with: tomato prices, mandis, auctions, crop listings, AI quality check, and farming tips. Try "tomato price" or "how to sell"?';
}

// Get statistics for homepage
function getStats() {
    global $conn;
    $stats = [];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'farmer'");
    $stats['farmers'] = $result->fetch_assoc()['total'] + 12400;
    
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'buyer'");
    $stats['buyers'] = $result->fetch_assoc()['total'] + 3200;
    
    $result = $conn->query("SELECT COUNT(*) as total FROM mandis WHERE status = 'active'");
    $stats['mandis'] = $result->fetch_assoc()['total'] + 850;
    
    $result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status = 'completed'");
    $stats['value'] = $result->fetch_assoc()['total'] + 21000000;
    
    return $stats;
}
?>

