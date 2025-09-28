<?php

// Script written partially with ChatGPT

require "db.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
    echo "Not authorized";
    exit;
}

$sellerId = $_SESSION["user_id"];
$requestId = intval($_POST["request_id"]);

// Get the trade request
$stmt = $pdo->prepare("SELECT * FROM trade_requests WHERE id = ?");
$stmt->execute([$requestId]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request || $request["seller_id"] != $sellerId) {
    http_response_code(403);
    echo "Access denied or request not found.";
    exit;
}

$buyerId = $request["buyer_id"];
$productId = $request["product_id"];
$price = $request["price"];

// Get ad details
$stmt = $pdo->prepare("SELECT * FROM ads WHERE id = ?");
$stmt->execute([$productId]);
$ad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ad) {
    echo "Ad not found.";
    exit;
}

if ($ad["status"] === "purchased") {
    echo "Product already purchased.";
    exit;
}

$photo = $ad["photo"];
$title = $ad["title"];

// Get buyer's balance
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$buyerId]);
$buyer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$buyer || $buyer["balance"] < $price) {
    echo "Buyer does not have enough funds to purchase.";
    exit;
}

// Start transaction
$pdo->beginTransaction();

try {
    // Deduct funds from buyer
    $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $stmt->execute([$price, $buyerId]);

    // Add funds to seller
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$price, $sellerId]);

    // Update ad status
    $stmt = $pdo->prepare("UPDATE ads SET status = 'purchased' WHERE id = ?");
    $stmt->execute([$productId]);

    // Create transaction record
    $stmt = $pdo->prepare("INSERT INTO transactions (ad_id, buyer_id, seller_id, price, photo, date) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$productId, $buyerId, $sellerId, $price, $photo]);

    // Get or create chat
    $stmt = $pdo->prepare("SELECT id FROM chats WHERE seller_id = ? AND buyer_id = ?");
    $stmt->execute([$sellerId, $buyerId]);
    $chatId = $stmt->fetchColumn();

    if (!$chatId) {
        $stmt = $pdo->prepare("INSERT INTO chats (seller_id, buyer_id) VALUES (?, ?)");
        $stmt->execute([$sellerId, $buyerId]);
        $chatId = $pdo->lastInsertId();
    }

    // Send purchase message
    $message = "The seller agreed to the price reduction. The product \"$title\" was sold for {$price}€. Please wait for the seller to ship the item.";
    $stmt = $pdo->prepare("INSERT INTO messages (chat_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$chatId, $sellerId, $message]);

    // Notify other buyers
    $stmt = $pdo->prepare("SELECT * FROM trade_requests WHERE product_id = ? AND id != ?");
    $stmt->execute([$productId, $requestId]);
    $otherRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($otherRequests as $other) {
        $otherBuyerId = $other["buyer_id"];

    // Get or create chat
    $stmt = $pdo->prepare("SELECT id FROM chats WHERE (seller_id = ? AND buyer_id = ?) OR (seller_id = ? AND buyer_id = ?)");
    $stmt->execute([$sellerId, $buyerId, $buyerId, $sellerId]);
    $chatId = $stmt->fetchColumn();
    
    if (!$chatId) {
        $stmt = $pdo->prepare("INSERT INTO chats (seller_id, buyer_id) VALUES (?, ?)");
        $stmt->execute([$sellerId, $buyerId]);
        $chatId = $pdo->lastInsertId();
    }

        // Decline message
        $declineMessage = "The product \"$title\" has been sold. Your price reduction offer was declined.";
        $stmt = $pdo->prepare("INSERT INTO messages (chat_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$chatId, $sellerId, $declineMessage]);
    }

    // Update current request as accepted
    $stmt = $pdo->prepare("UPDATE trade_requests SET status = 'accepted' WHERE id = ?");
    $stmt->execute([$requestId]);
    
    // Delete all trade requests for this product (including accepted one)
    $stmt = $pdo->prepare("DELETE FROM trade_requests WHERE product_id = ?");
    $stmt->execute([$productId]);

    $pdo->commit();
    echo "success";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Transaction failed: " . $e->getMessage();
}
?>