<?php
header('Content-Type: application/json');
require_once '../includes/db-connect.php';

session_start();

$auctionId = (int)($_POST['auction_id'] ?? 0);
$buyerId = $_SESSION['user_id'] ?? 0;

if (!$buyerId) {
    echo json_encode(['success' => false, 'error' => 'Please login to bid']);
    exit;
}

// Get auction
$stmt = $conn->prepare("SELECT * FROM auctions WHERE id = ? AND status = 'live'");
$stmt->bind_param("i", $auctionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Auction ended or not found']);
    exit;
}

$auction = $result->fetch_assoc();

// Check if ended
if (strtotime($auction['ends_at']) < time()) {
    $conn->query("UPDATE auctions SET status = 'ended' WHERE id = $auctionId");
    echo json_encode(['success' => false, 'error' => 'Auction has ended']);
    exit;
}

// Calculate new price (+5%)
$increment = round($auction['current_price'] * 0.05);
$newPrice = $auction['current_price'] + $increment;

// Record bid
$stmt = $conn->prepare("INSERT INTO bids (auction_id, buyer_id, bid_amount) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $auctionId, $buyerId, $newPrice);
$stmt->execute();

// Update auction
$stmt = $conn->prepare("UPDATE auctions SET current_price = ?, bids_count = bids_count + 1, winner_id = ? WHERE id = ?");
$stmt->bind_param("iii", $newPrice, $buyerId, $auctionId);
$stmt->execute();

echo json_encode(['success' => true, 'new_price' => $newPrice]);
?>

