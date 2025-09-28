<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST["login"]);  // Updated to match input name
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $gender = $_POST["gender"];
    $birth_date = $_POST["birth_date"];

    // Check if any of the required fields are empty
    if (empty($username) || empty($password) || empty($email) || empty($birth_date)) {
        echo '<script>alert("All fields are required!"); window.history.back();</script>';
        exit;
    }

    // Age check (16+ years)
    $birth_timestamp = strtotime($birth_date);
    $min_age_timestamp = strtotime("-16 years");

    if ($birth_timestamp > $min_age_timestamp) {
        echo '<script>alert("You must be older than 16 years to register!"); window.history.back();</script>';
        exit;
    }

    try {
        // Check for existing username or email
        $check_sql = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([":username" => $username, ":email" => $email]);
        $existing_count = $check_stmt->fetchColumn();

        if ($existing_count > 0) {
            echo '<script>alert("Username or email is already taken!"); window.history.back();</script>';
            exit;
        }

        // Password hashing
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Set default balance and role
        $balance = 2000;  // Default balance is 0
        $role = 'user';  // Default role is 'user'
        $registered_at = date("Y-m-d H:i:s");  // Current date and time

        // Insert new user into database
        $sql = "INSERT INTO users (username, email, password, balance, registered_at, role, gender, birth_date) 
                VALUES (:username, :email, :password, :balance, :registered_at, :role, :gender, :birth_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":username" => $username,
            ":email" => $email,
            ":password" => $hashed_password,
            ":balance" => $balance,
            ":registered_at" => $registered_at,
            ":role" => $role,
            ":gender" => $gender,
            ":birth_date" => $birth_date
        ]);

        echo '<script>
                alert("Registration successful!"); 
                setTimeout(function() { 
                    window.location.href = "../../public/index.php"; 
                }, 100);
              </script>';
    } catch (PDOException $e) {
        echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
    }
}
?>
