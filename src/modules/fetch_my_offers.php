<?php
session_start();
require 'db.php'; 

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 9;
$offset = ($page - 1) * $limit;




try {
    // Get user's ads with pagination
    $stmt = $pdo->prepare("SELECT p.id, p.title, p.photo, p.brief1, p.brief2, p.brief3, p.price, p.status,
                                    (SELECT COUNT(*) FROM trade_requests t WHERE t.product_id = p.id) AS trade_count
                               FROM ads p
                               WHERE p.user_id = :user_id
                               LIMIT $limit OFFSET $offset");
    $stmt->execute(['user_id' => $user_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total number of ads
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ads WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $totalItems = $stmt->fetchColumn();
    $totalPages = max(1, ceil($totalItems / $limit)); // min = 1 to avoid division by zero

    echo json_encode([
        "success" => true,
        "products" => $products,
        "current_page" => $page,
        "total_pages" => $totalPages
    ]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

?>
