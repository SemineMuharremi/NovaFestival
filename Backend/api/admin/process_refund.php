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
if (!isset($input['purchase_id'])) {
    echo 'Missing purchase ID';
    exit;
}

$purchaseId = intval($input['purchase_id']);

// Get purchase details
$query = "SELECT id, ticket_type_id, quantity, status FROM purchases WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $purchaseId);
$stmt->execute();
$result = $stmt->get_result();
$purchase = $result->fetch_assoc();

if (!$purchase) {
    echo 'Purchase not found';
    exit;
}

if ($purchase['status'] === 'cancelled') {
    echo 'Purchase is already cancelled';
    exit;
}

$conn->begin_transaction();

try {
    // Cancel the purchase
    $updateStmt = $conn->prepare("UPDATE purchases SET status = 'cancelled' WHERE id = ?");
    $updateStmt->bind_param('i', $purchaseId);
    $updateStmt->execute();

    // Restore ticket availability
    $restoreStmt = $conn->prepare("UPDATE ticket_types SET availability = availability + ? WHERE id = ?");
    $restoreStmt->bind_param('ii', $purchase['quantity'], $purchase['ticket_type_id']);
    $restoreStmt->execute();

    // Mark the payment as refunded
    $paymentStmt = $conn->prepare("UPDATE payments SET payment_status = 'refunded' WHERE purchase_id = ?");
    $paymentStmt->bind_param('i', $purchaseId);
    $paymentStmt->execute();

    $conn->commit();

    echo 'SUCCESS:Refund processed for purchase ' . $purchaseId;
} catch (Exception $e) {
    $conn->rollback();
    echo 'Error processing refund';
}
