<?php
require "db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['user_id'];
$ad_id = isset($_POST['offer_id']) ? intval($_POST['offer_id']) : 0;

if ($ad_id <= 0) {
    die("Invalid ad ID.");
}

// Get ad data to ensure it belongs to the user
$query = $pdo->prepare("SELECT * FROM ads WHERE id = :ad_id AND user_id = :user_id");
$query->execute(['ad_id' => $ad_id, 'user_id' => $user_id]);
$ad = $query->fetch(PDO::FETCH_ASSOC);

if (!$ad) {
    die("Ad not found or you don't have permission to edit it.");
}

// Get new data from the form
$title = trim($_POST['offername']);
$brief_info_1 = trim($_POST['brief-information-1']);
$brief_info_2 = trim($_POST['brief-information-2']);
$brief_info_3 = trim($_POST['brief-information-3']);
$description = trim($_POST['description']);
$price = trim($_POST['price']);
$image = $ad['photo']; // Use the old image if a new one is not uploaded

// Handle new image upload
if (!empty($_FILES['file']['name'])) {
    $upload_dir =  dirname(__DIR__) . "/uploads/";
    $image_name = time() . "_" . basename($_FILES['file']['name']);
    $target_path = $upload_dir . $image_name;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
        $image = $image_name;
    } else {
        die("Error uploading image.");
    }
}

// Update data in the database
$update = $pdo->prepare("UPDATE ads SET 
    title = :title, 
    brief1 = :brief1, 
    brief2 = :brief2, 
    brief3 = :brief3, 
    description = :description, 
    price = :price, 
    photo = :photo 
    WHERE id = :ad_id AND user_id = :user_id");

$update->execute([
    'title' => $title,
    'brief1' => $brief_info_1,
    'brief2' => $brief_info_2,
    'brief3' => $brief_info_3,
    'description' => $description,
    'price' => $price,
    'photo' => $image,
    'ad_id' => $ad_id,
    'user_id' => $user_id
]);


echo '<script>
            alert("Listing has been successfully updated!"); 
            setTimeout(function() { 
                window.location.href = "../../public/my-offers.php";
            }, 100);
        </script>';
exit();
?>