<?php
require "db.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to create an ad.");
    
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $offername = trim($_POST["offername"]);
    $category_name = trim($_POST["category"]);
    $brief1 = trim($_POST["brief-information-1"]);
    $brief2 = trim($_POST["brief-information-2"]);
    $brief3 = trim($_POST["brief-information-3"]);
    $description = trim($_POST["description"]);
    $price = trim($_POST["price"]);
    $user_id = $_SESSION["user_id"];

    // Check if all fields are filled
    if (empty($offername) || empty($category_name) || empty($brief1) || empty($brief2) || empty($brief3) || empty($description) || empty($price)) {
        echo '<script>alert("All fields are required"); window.history.back();</script>';
        exit;
    }

    // Get category ID
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$category_name]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        die("Invalid category.");
    }

    $category_id = $category["id"];

    // Check image upload
    if (!isset($_FILES["file"]) || $_FILES["file"]["error"] != 0) {
        echo '<script>alert("An image is required"); window.history.back();</script>';
        exit;
    }

    $image = $_FILES["file"];

    // Check if the file is PNG
    $allowed_types = ["image/png"];
    if (!in_array($image["type"], $allowed_types)) {
        die("Only PNG images are allowed.");
    }

    // Insert record into the database (without photo, to get ID)
    $stmt = $pdo->prepare("INSERT INTO ads (user_id, title, category_id, brief1, brief2, brief3, description, price, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $offername, $category_id, $brief1, $brief2, $brief3, $description, $price]);

    // Get the ID of the created ad
    $ad_id = $pdo->lastInsertId();

    // Generate photo name
    $image_name = "offer-image-" . $ad_id . ".png";
    $image_path = "../uploads/" . $image_name;

    // Move the uploaded file
    if (!move_uploaded_file($image["tmp_name"], $image_path)) {
        die("Failed to upload image.");
    }

    // Update the record in the database, adding the file name
    $stmt = $pdo->prepare("UPDATE ads SET photo = ? WHERE id = ?");
    $stmt->execute([$image_name, $ad_id]);

    echo '<script>alert("Ad created successfully!"); window.location.href = "../public/index.php";</script>';
}
?>