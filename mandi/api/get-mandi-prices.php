<?php
header('Content-Type: application/json');
require_once '../includes/db-connect.php';

$sql = "SELECT mp.*, m.mandi_name, m.distance_km 
    FROM mandi_prices mp 
    JOIN mandis m ON mp.mandi_id = m.id 
    WHERE mp.price_date = CURDATE() 
    ORDER BY mp.modal_price DESC";

$result = $conn->query($sql);
$prices = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['success' => true, 'prices' => $prices]);
?>

