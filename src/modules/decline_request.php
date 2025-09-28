<?php
require "db.php";
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['request_id']) || empty($data['request_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request ID"]);
    exit();
}

$request_id = intval($data['request_id']);

// Retrieve the request information
$stmt = $pdo->prepare("SELECT * FROM trade_requests WHERE id = ?");
$stmt->execute([$request_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    echo json_encode(["success" => false, "message" => "Offer not found"]);
    exit();
}

// Determine the buyer's ID
$buyer_id = $request['buyer_id'];

// Find the chat between the seller and buyer
$chat_stmt = $pdo->prepare("SELECT id FROM chats WHERE (seller_id = ? AND buyer_id = ?) OR (seller_id = ? AND buyer_id = ?)");
$chat_stmt->execute([$user_id, $buyer_id, $buyer_id, $user_id]);
$chat = $chat_stmt->fetch(PDO::FETCH_ASSOC);

if (!$chat) {
    echo json_encode(["success" => false, "message" => "Chat not found"]);
    exit();
}

$chat_id = $chat['id'];

// Delete the request
$delete_stmt = $pdo->prepare("DELETE FROM trade_requests WHERE id = ?");
$delete_stmt->execute([$request_id]);

// Send a message to the chat
$message = "Your price change request has been declined by the seller.";
$insert_msg_stmt = $pdo->prepare("INSERT INTO messages (chat_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())");
$insert_msg_stmt->execute([$chat_id, $user_id, $message]);

echo json_encode(["success" => true]);
?>