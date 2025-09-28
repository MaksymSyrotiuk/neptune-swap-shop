<?php
session_start();
require 'db.php'; // 

$currentUserId = $_SESSION['user_id']; // Current user ID
$chatId = $_GET['chat_id'] ?? null;

if (!$chatId) {
    echo json_encode(['error' => 'Chat ID is required']);
    exit;
}

// Get chat participants
$sql = "SELECT seller_id, buyer_id FROM chats WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$chatId]);
$chat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chat) {
    echo json_encode(['error' => 'Chat not found']);
    exit;
}

// Check if the chat belongs to the current user
if ($chat['seller_id'] != $currentUserId && $chat['buyer_id'] != $currentUserId) {
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Get messages for the chat, sorted from oldest to newest
$sql = "SELECT sender_id, message, created_at FROM messages
          WHERE chat_id = ?
          ORDER BY created_at ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$chatId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add message_type field (my-message / user-message)
foreach ($messages as &$message) {
    $message['message_type'] = ($message['sender_id'] == $currentUserId) ? 'my-message' : 'user-message';
}

echo json_encode($messages);
?>