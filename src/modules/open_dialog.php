<?php

// Script written partially with ChatGPT

session_start();
require 'db.php';

header('Content-Type: application/json; charset=utf-8');

// Make sure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
    exit;
}

// Verify that the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$buyer_id = $_SESSION['user_id'];

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required parameters
$product_id = isset($data['product_id']) ? intval($data['product_id']) : 0;
$seller_id  = isset($data['seller_id']) ? intval($data['seller_id']) : 0;
$product_title = isset($data['product_title']) ? trim($data['product_title']) : '';

if ($product_id === 0 || $seller_id === 0 || empty($product_title)) {
    echo json_encode(["success" => false, "error" => "Missing parameters"]);
    exit;
}

// Prevent a user from opening a chat with themselves
if ($buyer_id === $seller_id) {
    echo json_encode(["success" => false, "error" => "Invalid user roles"]);
    exit;
}

// Check if a chat already exists between the buyer and seller (order-independent)
$sql = "SELECT id FROM chats 
        WHERE (seller_id = :seller AND buyer_id = :buyer) 
           OR (seller_id = :buyer AND buyer_id = :seller)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'seller' => $seller_id,
    'buyer'  => $buyer_id
]);
$chat = $stmt->fetch(PDO::FETCH_ASSOC);

if ($chat) {
    $chat_id = $chat['id'];
} else {
    // No chat exists, so create a new one using the seller's and buyer's IDs
    $stmt = $pdo->prepare("INSERT INTO chats (seller_id, buyer_id, created_at) VALUES (:seller, :buyer, NOW())");
    $stmt->execute(['seller' => $seller_id, 'buyer' => $buyer_id]);
    $chat_id = $pdo->lastInsertId();

    // Optionally, insert an initial message from the buyer with a question regarding the product.
    $initial_message = "I have questions regarding your product \"$product_title\".";
    $stmt = $pdo->prepare("INSERT INTO messages (chat_id, sender_id, message, created_at) VALUES (:chat_id, :sender, :message, NOW())");
    $stmt->execute([
        'chat_id' => $chat_id,
        'sender'  => $buyer_id,
        'message' => $initial_message
    ]);
}

echo json_encode(["success" => true, "chat_id" => $chat_id]);
?>