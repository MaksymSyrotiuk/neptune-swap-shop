<?php
//// Script written partially with ChatGPT

require "db.php";

session_start(); 
header('Content-Type: application/json; charset=utf-8');


$currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

// Set limit and pagination
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 9;
$limit = max(1, min(100, $limit)); // Limit from 1 to 100
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Filters
$search = isset($_GET['search']) ? "%" . trim($_GET['search']) . "%" : "%";
$category = isset($_GET['category']) && $_GET['category'] !== "all" ? explode(',', $_GET['category']) : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : "no";

// Check sort for allowed values
$allowedSorts = ["cheap", "expensive", "first", "last"];
$sort = in_array($sort, $allowedSorts) ? $sort : "no";

// Main SQL query with JOIN and user check
$queryStr = "SELECT ads.*, users.username, (ads.user_id = :current_user_id) as isOwnProduct FROM ads " . 
            "JOIN users ON ads.user_id = users.id " . 
            "WHERE ads.title LIKE :search AND ads.status = 'active'";

$params = [":search" => $search, ":limit" => $limit, ":offset" => $offset, ":current_user_id" => $currentUserId];

// Category filter
if ($category) {
    $categoryPlaceholders = [];
    foreach ($category as $index => $cat) {
        $paramName = ":category$index";
        $categoryPlaceholders[] = $paramName;
        $params[$paramName] = (int)$cat; // Cast to int
    }
    $queryStr .= " AND ads.category_id IN (" . implode(',', $categoryPlaceholders) . ")";
}

// Sorting
switch ($sort) {
    case "cheap":
        $queryStr .= " ORDER BY ads.price ASC";
        break;
    case "expensive":
        $queryStr .= " ORDER BY ads.price DESC";
        break;
    case "first":
        $queryStr .= " ORDER BY ads.title ASC";
        break;
    case "last":
        $queryStr .= " ORDER BY ads.title DESC";
        break;
}

// Add limit and offset
$queryStr .= " LIMIT :limit OFFSET :offset";

// Execute the main query
$query = $pdo->prepare($queryStr);
foreach ($params as $key => $value) {
    $query->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$query->execute();
$products = $query->fetchAll(PDO::FETCH_ASSOC);

// Count the total number of products
$totalQueryStr = "SELECT COUNT(*) as total FROM ads WHERE title LIKE :search AND status = 'active'";
$totalParams = [":search" => $search];

if ($category) {
    $categoryPlaceholders = [];
    foreach ($category as $index => $cat) {
        $paramName = ":category$index";
        $categoryPlaceholders[] = $paramName;
        $totalParams[$paramName] = (int)$cat;
    }
    $totalQueryStr .= " AND category_id IN (" . implode(',', $categoryPlaceholders) . ")";
}

$totalQuery = $pdo->prepare($totalQueryStr);
foreach ($totalParams as $key => $value) {
    $totalQuery->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$totalQuery->execute();
$totalProducts = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalProducts / $limit);

// Return JSON
echo json_encode([
    "products" => $products,
    "totalPages" => $totalPages
]);

?>
