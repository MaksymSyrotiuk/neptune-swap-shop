<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$currentUserId = $_SESSION['user_id'];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get the total number of chats
$countStmt = $pdo->prepare("
    SELECT COUNT(*) FROM chats 
    WHERE seller_id = :user_id OR buyer_id = :user_id
");
$countStmt->execute(['user_id' => $currentUserId]);
$totalChats = (int)$countStmt->fetchColumn();
$totalPages = ceil($totalChats / $limit);

// Get chats with limit
$stmt = $pdo->prepare("
    SELECT 
        c.id AS chat_id,
        CASE
            WHEN c.seller_id = :user_id THEN u_buyer.username
            ELSE u_seller.username
        END AS username,
        (
            SELECT COUNT(*) FROM messages m
            WHERE m.chat_id = c.id
              AND m.sender_id != :user_id
              AND m.is_read = 0
        ) AS unread_count
    FROM chats c
    JOIN users u_seller ON u_seller.id = c.seller_id
    JOIN users u_buyer ON u_buyer.id = c.buyer_id
    WHERE c.seller_id = :user_id OR c.buyer_id = :user_id
    ORDER BY c.created_at DESC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'chats' => $chats,
    'total_pages' => $totalPages,
    'current_page' => $page
]);
?>