<?php
session_start();
include '../../config/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo 'Unauthorized';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Invalid request method';
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['purchase_id']) || !isset($input['status'])) {
    echo 'Missing required fields';
    exit;
}

$purchaseId = intval($input['purchase_id']);
$newStatus = $input['status'];

// Validate status
$allowedStatuses = ['pending', 'confirmed', 'cancelled'];
if (!in_array($newStatus, $allowedStatuses)) {
    echo 'Invalid status';
    exit;
}

// Update purchase status using prepared statement
$updateQuery = "UPDATE purchases SET status = ? WHERE id = ?";
$stmt = $conn->prepare($updateQuery);

if (!$stmt) {
    echo 'Prepare error: ' . $conn->error;
    exit;
}

$stmt->bind_param('si', $newStatus, $purchaseId);

if ($stmt->execute()) {
    echo 'SUCCESS:' . $newStatus;
} else {
    echo 'Error: ' . $stmt->error;
}
