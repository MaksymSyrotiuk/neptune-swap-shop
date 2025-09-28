<?php
session_start();
require 'db.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

// Rate limiting to prevent abuse
if (isset($_SESSION['last_toggle']) && time() - $_SESSION['last_toggle'] < 2) {
    echo json_encode(["success" => false, "error" => "Too many requests. Please wait."]);
    exit;
}
$_SESSION['last_toggle'] = time();

// Get offer ID and enable flag from request
$data = json_decode(file_get_contents("php://input"), true);
$offerId = isset($data['id']) ? intval($data['id']) : 0;
$enable = isset($data['enable']) ? (bool)$data['enable'] : false;

// Validate offer ID
if ($offerId === 0) {
    echo json_encode(["success" => false, "error" => "Invalid offer ID"]);
    exit;
}

// Determine new status
$newStatus = $enable ? "active" : "inactive";

try {
    // Update offer status in database
    $stmt = $pdo->prepare("UPDATE ads SET status = :status WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['status' => $newStatus, 'id' => $offerId, 'user_id' => $_SESSION['user_id']]);

    // Check if update was successful
    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "status" => $newStatus]);
    } else {
        echo json_encode(["success" => false, "error" => "Offer not found or already set"]);
    }
} catch (Exception $e) {
    // Handle database errors
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>