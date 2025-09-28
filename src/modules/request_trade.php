<?php
session_start();
require 'db.php'; // Database connection (PDO)

header('Content-Type: application/json; charset=utf-8');

// Read JSON from the request body
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "error" => "User not logged in"]);
        exit;
    }
    $buyer_id = intval($_SESSION['user_id']); // Buyer ID from session

    // Get data from the request
    $seller_id = isset($data['seller_id']) ? intval($data['seller_id']) : 0;
    $product_id = isset($data['product_id']) ? intval($data['product_id']) : 0;
    $price = isset($data['requested_price']) ? floatval($data['requested_price']) : 0;

    if (!$seller_id || !$product_id || !$price) {
        echo json_encode(["success" => false, "error" => "Missing parameters"]);
        exit;
    }

    // Check if the buyer is the owner of the listing
    if ($buyer_id === $seller_id) {
        echo json_encode(["success" => false, "error" => "This is your listing, you cannot request a price reduction."]);
        exit;
    }

    try {
        // Get buyer's balance
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$buyer_id]);
        $buyer = $stmt->fetch();

        if (!$buyer) {
            echo json_encode(["success" => false, "error" => "User not found"]);
            exit;
        }

        $buyer_balance = floatval($buyer['balance']);

        // Check if there are sufficient funds
        if ($buyer_balance < $price) {
            echo json_encode(["success" => false, "error" => "Insufficient funds to request a price reduction."]);
            exit;
        }

        // Check if a request for this product from this buyer already exists
        $stmt = $pdo->prepare("SELECT id FROM trade_requests WHERE buyer_id = ? AND product_id = ?");
        $stmt->execute([$buyer_id, $product_id]);
        $existingRequest = $stmt->fetch();

        if ($existingRequest) {
            echo json_encode(["success" => false, "error" => "Request already sent."]);
            exit;
        }

        // Create a trade request
        $stmt = $pdo->prepare("INSERT INTO trade_requests (buyer_id, seller_id, product_id, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$buyer_id, $seller_id, $product_id, $price]);
        $requestId = $pdo->lastInsertId();

        // Check if a chat exists between the users (without product context)
        $stmt = $pdo->prepare("SELECT id FROM chats WHERE (buyer_id = ? AND seller_id = ?) OR (buyer_id = ? AND seller_id = ?)");
        $stmt->execute([$buyer_id, $seller_id, $seller_id, $buyer_id]);
        $chat = $stmt->fetch();

        if (!$chat) {
            // Create a new chat if it doesn't exist
            $stmt = $pdo->prepare("INSERT INTO chats (buyer_id, seller_id) VALUES (?, ?)");
            $stmt->execute([$buyer_id, $seller_id]);
            $chatId = $pdo->lastInsertId();
        } else {
            $chatId = $chat['id'];
        }

        // Get buyer's username
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$buyer_id]);
        $buyer = $stmt->fetch();
        $buyer_name = $buyer ? $buyer['username'] : "Unknown Buyer";

        // Get product title
        $stmt = $pdo->prepare("SELECT title FROM ads WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        $product_name = $product ? $product['title'] : "Unknown Product";

        // Format the chat message
        $message = "Buyer \"$buyer_name\" requested a price reduction for \"$product_name\" by {$price}€.";

        $stmt = $pdo->prepare("INSERT INTO messages (chat_id, sender_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$chatId, $buyer_id, $message]);

        echo json_encode(["success" => true, "error" => "Request sent!", "request_id" => $requestId]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
?>