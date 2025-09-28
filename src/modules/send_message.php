<?php
session_start();
require_once 'db.php';

// Check if user is authorized
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit;
}

// Get data from the request
$data = json_decode(file_get_contents("php://input"), true);

$chatId = intval($data['chat_id']);
$message = trim($data['message']);
$senderId = $_SESSION['user_id'];

if (!$chatId || !$message) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

// Get seller and buyer from the chat
$stmt = $pdo->prepare("SELECT seller_id, buyer_id FROM chats WHERE id = ?");
$stmt->execute([$chatId]);
$chat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chat) {
    echo json_encode(['success' => false, 'error' => 'Chat not found']);
    exit;
}

// Check if the sender is a participant in the chat
if ($senderId != $chat['seller_id'] && $senderId != $chat['buyer_id']) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Add message to the database
$stmt = $pdo->prepare("INSERT INTO messages (chat_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$chatId, $senderId, $message]);

echo json_encode(['success' => true]);
?>