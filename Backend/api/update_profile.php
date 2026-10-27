<?php
session_start();
include "../config/db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: ../../tickets.php");
  exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $password = $_POST['password'];
  
  // Validate inputs
  if (empty($email) || empty($phone)) {
    $_SESSION['error'] = "Email and phone are required.";
    header("Location: ../../profile.php");
    exit();
  }
  
  // Build update query
  $update_query = "UPDATE users SET email = ?, phone = ?";
  $params = array($email, $phone);
  $types = "ss";
  
  // If password is provided, update it
  if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $update_query .= ", password = ?";
    $params[] = $hashed_password;
    $types .= "s";
  }
  
  $update_query .= " WHERE id = ?";
  $params[] = $user_id;
  $types .= "i";
  
  // Prepare and execute
  $stmt = $conn->prepare($update_query);
  $stmt->bind_param($types, ...$params);
  
  if ($stmt->execute()) {
    $_SESSION['success'] = "Profile updated successfully!";
  } else {
    $_SESSION['error'] = "Error updating profile: " . $conn->error;
  }
  
  header("Location: ../../profile.php");
  exit();
}
?>
