<?php
header('Content-Type: application/json');
require_once '../includes/functions.php';

$question = $_POST['question'] ?? '';
$answer = chatbotReply($question);

// Log to database if user is logged in
session_start();
if (isset($_SESSION['user_id'])) {
    require_once '../includes/db-connect.php';
    $stmt = $conn->prepare("INSERT INTO chatbot_logs (user_id, question, answer) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION['user_id'], $question, $answer);
    $stmt->execute();
}

echo json_encode(['success' => true, 'answer' => $answer]);
?>

