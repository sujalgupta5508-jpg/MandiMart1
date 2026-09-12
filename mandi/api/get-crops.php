<?php
header('Content-Type: application/json');
require_once '../includes/db-connect.php';

$sql = "SELECT c.*, u.full_name as seller_name, u.rating, u.mandi_name 
    FROM crop_listings c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.status = 'active' 
    ORDER BY c.created_at DESC LIMIT 50";

$result = $conn->query($sql);
$crops = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['success' => true, 'crops' => $crops]);
?>

