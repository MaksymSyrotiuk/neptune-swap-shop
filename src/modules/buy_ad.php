<?php
session_start();
require_once 'db.php';

$ad_id = $_POST['ad_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "error" => "User not logged in"]);
        exit;
    }
}
$buyer_id = $_SESSION['user_id'];


if (!$ad_id || !$buyer_id) {
    echo json_encode(["success" => false, "error" => "Missing data"]);
    exit;
}


// Get product and price
$stmt = $pdo->prepare("SELECT price, user_id as seller_id, photo, status, title FROM ads WHERE id = ?");
$stmt->execute([$ad_id]);
$ad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ad) {
    echo json_encode(["success" => false, "error" => "Ad not found"]);
    exit;
}
if ($ad['status'] === 'purchased') {
    echo json_encode(["success" => false, "error" => "Already purchased"]);
    exit;
}

$price = $ad['price'];
$seller_id = $ad['seller_id'];
$photo = $ad['photo'];
$title = $ad['title']; 

// Check buyer's balance
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$buyer_id]);
$buyer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$buyer || $buyer['balance'] < $price) {
    echo json_encode(["success" => false, "error" => "Insufficient balance"]);
    exit;
}

// Begin transaction
$pdo->beginTransaction();

try {
    // Deduct money from buyer
    $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $stmt->execute([$price, $buyer_id]);

    // Add money to seller
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$price, $seller_id]);

    // Update ad status
    $stmt = $pdo->prepare("UPDATE ads SET status = 'purchased' WHERE id = ?");
    $stmt->execute([$ad_id]);

    // Create transaction record
    $stmt = $pdo->prepare("INSERT INTO transactions (ad_id, buyer_id, seller_id, price, photo, date) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$ad_id, $buyer_id, $seller_id, $price, $photo]);


    $user_id = $_SESSION['user_id'];

    // Get buyer's username
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$buyer_id]);
    $buyer_user = $stmt->fetch(PDO::FETCH_ASSOC);
    $buyer_name = $buyer_user['username'] ?? 'A buyer';

    // Find chat
    $chat_stmt = $pdo->prepare("SELECT id FROM chats WHERE (seller_id = ? AND buyer_id = ?) OR (seller_id = ? AND buyer_id = ?)");
    $chat_stmt->execute([$seller_id, $buyer_id, $buyer_id, $seller_id]);
    $chat = $chat_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$chat) {
        echo json_encode(["success" => false, "message" => "Chat not found"]);
        exit();
    }

    $chat_id = $chat['id'];

    // Message
    $message = "Customer \"$buyer_name\" has purchased your product \"$title\". Please send the item to the buyer and confirm shipment.";

    // Insert into chat
    $insert_msg_stmt = $pdo->prepare("INSERT INTO messages (chat_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())");
    $insert_msg_stmt->execute([$chat_id, $user_id, $message]);

    $pdo->commit();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["success" => false, "error" => "Transaction failed: " . $e->getMessage()]);
}

?>