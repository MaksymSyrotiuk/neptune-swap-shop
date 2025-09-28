<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$adId = intval($data['ad_id'] ?? 0);
$action = $data['action'] ?? '';

if (!isset($_SESSION['user_id']) || $adId === 0 || !in_array($action, ['ship', 'deliver'])) {
    echo json_encode(["success" => false, "error" => "Invalid data"]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Get transaction data
    $stmt = $pdo->prepare("SELECT t.id AS transaction_id, t.buyer_id, t.seller_id, a.title FROM transactions t JOIN ads a ON t.ad_id = a.id WHERE t.ad_id = :ad_id");
    $stmt->execute(['ad_id' => $adId]);
    $transaction = $stmt->fetch();

    if (!$transaction) {
        echo json_encode(["success" => false, "error" => "Transaction not found"]);
        exit;
    }

    $buyerId = $transaction['buyer_id'];
    $sellerId = $transaction['seller_id'];
    $title = $transaction['title'];

    // Check access and update status
    if ($action === 'ship') {
        if ($sellerId != $userId) {
            echo json_encode(["success" => false, "error" => "Access denied"]);
            exit;
        }
        $status = 'shipped';
        $message = "Seller has shipped the product \"$title\".";
        $senderId = $sellerId;


    } elseif ($action === 'deliver') {
        if ($buyerId != $userId) {
            echo json_encode(["success" => false, "error" => "Access denied"]);
            exit;
        }
        $status = 'delivered';
        $message = "Buyer has received the product \"$title\".";
        $senderId = $buyerId;

    }

    // Update ad status
    $update = $pdo->prepare("UPDATE ads SET status = :status WHERE id = :id");
    $update->execute(['status' => $status, 'id' => $adId]);

    // Find or create chat between buyer and seller
    $stmt = $pdo->prepare("SELECT id FROM chats WHERE (buyer_id = :buyer AND seller_id = :seller) OR (buyer_id = :seller AND seller_id = :buyer)");
    $stmt->execute(['buyer' => $buyerId, 'seller' => $sellerId]);
    $chat = $stmt->fetch();

    if (!$chat) {
        $stmt = $pdo->prepare("INSERT INTO chats (buyer_id, seller_id) VALUES (:buyer, :seller)");
        $stmt->execute(['buyer' => $buyerId, 'seller' => $sellerId]);
        $chatId = $pdo->lastInsertId();
    } else {
        $chatId = $chat['id'];
    }

    // Add message to chat
    $stmt = $pdo->prepare("INSERT INTO messages (chat_id, sender_id, message) VALUES (:chat_id, :sender_id, :message)");
    $stmt->execute([
        'chat_id' => $chatId,
        'sender_id' => $senderId,
        'message' => $message
    ]);

    echo json_encode(["success" => true, "new_status" => $status]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>