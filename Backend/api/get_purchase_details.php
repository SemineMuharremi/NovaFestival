<?php
session_start();
include '../config/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Error:Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    die('Error:Invalid request method');
}

$purchase_id = intval($_GET['id'] ?? 0);

if ($purchase_id <= 0) {
    die('Error:Invalid purchase ID');
}

// Get purchase details with user and ticket info
$query = "SELECT p.id, p.user_id, p.ticket_type_id, p.quantity, p.total_price, p.purchase_date, p.status,
                  u.name, u.email,
                  t.ticket_name, t.price
           FROM purchases p
           JOIN users u ON p.user_id = u.id
           JOIN ticket_types t ON p.ticket_type_id = t.id
           WHERE p.id = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Error:Prepare error: ' . $conn->error);
}

$stmt->bind_param('i', $purchase_id);
if (!$stmt->execute()) {
    die('Error:Query error: ' . $stmt->error);
}

$result = $stmt->get_result();
$purchase = $result->fetch_assoc();

if (!$purchase) {
    die('Error:Purchase not found');
}

// Return success with pipe-delimited data
$response = 'SUCCESS:' .
    $purchase['id'] . '|' . 
    $purchase['name'] . '|' . 
    $purchase['email'] . '|' . 
    $purchase['ticket_name'] . '|' . 
    $purchase['quantity'] . '|' . 
    $purchase['purchase_date'] . '|' . 
    $purchase['total_price'];

die($response);
?>


