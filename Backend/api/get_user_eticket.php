<?php
session_start();
include '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die('Error:Not logged in');
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    die('Error:Invalid request method');
}

$purchase_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($purchase_id <= 0) {
    die('Error:Invalid purchase ID');
}

// Get purchase details - verify it belongs to the user
$query = "SELECT p.id, p.user_id, p.ticket_type_id, p.quantity, p.total_price, p.purchase_date, p.status,
                  u.name, u.email,
                  t.ticket_name, t.price,
                  et.eticket_token
           FROM purchases p
           JOIN users u ON p.user_id = u.id
           JOIN ticket_types t ON p.ticket_type_id = t.id
           LEFT JOIN e_tickets et ON p.id = et.purchase_id
           WHERE p.id = ? AND p.user_id = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Error:Database error: ' . $conn->error);
}

$stmt->bind_param('ii', $purchase_id, $user_id);
if (!$stmt->execute()) {
    die('Error:Query failed: ' . $stmt->error);
}

$result = $stmt->get_result();
$purchase = $result->fetch_assoc();

if (!$purchase) {
    die('Error:Purchase not found');
}

// Check if purchase is confirmed
if ($purchase['status'] !== 'confirmed') {
    die('Error:E-ticket only available for confirmed purchases');
}

// If e-ticket doesn't exist yet, generate it now
if (!$purchase['eticket_token']) {
    // Generate unique e-ticket token
    $eticket_token = 'NOVA-' . strtoupper(bin2hex(random_bytes(5)));
    
    // Insert into e_tickets table
    $insert_query = "INSERT INTO e_tickets (purchase_id, eticket_token, status) 
                     VALUES (?, ?, 'active')
                     ON DUPLICATE KEY UPDATE eticket_token = VALUES(eticket_token)";
    
    $insert_stmt = $conn->prepare($insert_query);
    if (!$insert_stmt) {
        die('Error:Failed to generate e-ticket: ' . $conn->error);
    }
    
    $insert_stmt->bind_param('is', $purchase_id, $eticket_token);
    if (!$insert_stmt->execute()) {
        die('Error:Failed to store e-ticket: ' . $insert_stmt->error);
    }
    
    $purchase['eticket_token'] = $eticket_token;
}

// Return success with pipe-delimited data
$response = 'SUCCESS:' . 
    $purchase['eticket_token'] . '|' . 
    $purchase['id'] . '|' . 
    $purchase['name'] . '|' . 
    $purchase['email'] . '|' . 
    $purchase['ticket_name'] . '|' . 
    $purchase['quantity'] . '|' . 
    $purchase['purchase_date'] . '|' . 
    $purchase['total_price'];

die($response);
?>


