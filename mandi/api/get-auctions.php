<?php
header('Content-Type: application/json');
require_once '../includes/db-connect.php';

// Update ended auctions first
$conn->query("UPDATE auctions SET status = 'ended' WHERE ends_at < NOW() AND status = 'live'");

$sql = "SELECT a.*, u.full_name as seller_name, 
    (SELECT u2.full_name FROM users u2 WHERE u2.id = a.winner_id) as winner_name
    FROM auctions a 
    JOIN users u ON a.user_id = u.id 
    WHERE a.status = 'live' OR a.ends_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ORDER BY a.status = 'live' DESC, a.ends_at ASC";

$result = $conn->query($sql);
$auctions = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['success' => true, 'auctions' => $auctions]);
?>

