<?php
session_start();
include "../config/db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = "Please log in to add a payment method.";
  header("Location: ../../profile.php");
  exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $payment_method = trim($_POST['payment_method']);
  $cardholder_name = trim($_POST['cardholder_name']);
  $card_number = trim($_POST['card_number']);
  $expiry_date = trim($_POST['expiry_date']);
  $cvc = trim($_POST['cvc']);
  
  // Valid payment methods that match database ENUM
  $valid_methods = ['credit_card', 'debit_card', 'paypal', 'bank_transfer'];
  
  // Validate inputs
  if (empty($payment_method) || empty($cardholder_name) || empty($card_number) || empty($expiry_date) || empty($cvc)) {
    $_SESSION['error'] = "All payment fields are required.";
    header("Location: ../../profile.php");
    exit();
  }
  
  // Validate payment method
  if (!in_array($payment_method, $valid_methods)) {
    $_SESSION['error'] = "Invalid payment method selected.";
    header("Location: ../../profile.php");
    exit();
  }
  
  if (strlen($card_number) < 3) {
    $_SESSION['error'] = "Card number must be at least 3 characters.";
    header("Location: ../../profile.php");
    exit();
  }
  
  $_SESSION['success'] = "✅ Payment method saved successfully!";
  header("Location: ../../profile.php");
  exit();
  }
?>
