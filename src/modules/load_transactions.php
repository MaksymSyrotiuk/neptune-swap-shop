<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Check if user is authorized
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not authorized"]);
    exit;
}

// Get request parameters
$type = $_GET['type'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1)); // Ensure page is at least 1
$perPage = 9;
$offset = ($page - 1) * $perPage;

$userId = $_SESSION['user_id'];

// Determine query based on transaction type
if ($type === 'sold') {
    $stmt = $pdo->prepare("SELECT t.*, a.status, a.title, a.photo, u.username AS buyer_name
                            FROM transactions t
                            JOIN ads a ON t.ad_id = a.id
                            JOIN users u ON u.id = t.buyer_id
                            WHERE t.seller_id = :id
                            ORDER BY t.date DESC
                            LIMIT :limit OFFSET :offset");
} elseif ($type === 'purchased') {
    $stmt = $pdo->prepare("SELECT t.*, a.status, a.title, a.photo, u.username AS seller_name
                            FROM transactions t
                            JOIN ads a ON t.ad_id = a.id
                            JOIN users u ON u.id = t.seller_id
                            WHERE t.buyer_id = :id
                            ORDER BY t.date DESC
                            LIMIT :limit OFFSET :offset");
} else {
    echo json_encode(["success" => false, "error" => "Invalid type"]);
    exit;
}

// Bind parameters and execute query
$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return transaction data as JSON
echo json_encode(["success" => true, "transactions" => $transactions]);
?>