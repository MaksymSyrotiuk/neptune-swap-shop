<?php
session_start();
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$chatId = intval($data['chat_id']);
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    UPDATE messages
    SET is_read = 1
    WHERE chat_id = ? AND sender_id != ?
");
$stmt->execute([$chatId, $userId]);

echo json_encode(['success' => true]);
?>