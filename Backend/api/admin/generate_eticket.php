<?php
session_start();
include '../../config/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = 'Unauthorized';
    header('Location: ../../admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Invalid request method';
    header('Location: ../../admin.php');
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$purchase_id = intval($input['purchase_id'] ?? 0);

if ($purchase_id <= 0) {
    echo 'Invalid purchase ID';
    exit;
}

// Get purchase details with user and ticket info
$purchase_query = "SELECT p.id, p.user_id, p.ticket_type_id, p.quantity, p.total_price, p.purchase_date, p.status,
                          u.name, u.email, 
                          t.ticket_name, t.price
                   FROM purchases p
                   JOIN users u ON p.user_id = u.id
                   JOIN ticket_types t ON p.ticket_type_id = t.id
                   WHERE p.id = ?";
$stmt = $conn->prepare($purchase_query);
$stmt->bind_param('i', $purchase_id);
$stmt->execute();
$result = $stmt->get_result();
$purchase = $result->fetch_assoc();

if (!$purchase) {
    echo 'Purchase not found';
    exit;
}


// Generate E-Ticket Token
$eticket_token = 'ETICKET-' . strtoupper(bin2hex(random_bytes(8))) . '-' . $purchase_id;

// Save e-ticket to database
$save_eticket_query = "INSERT INTO e_tickets (purchase_id, eticket_token, status) VALUES (?, ?, 'active') ON DUPLICATE KEY UPDATE eticket_token = VALUES(eticket_token), generated_at = CURRENT_TIMESTAMP";
$save_stmt = $conn->prepare($save_eticket_query);

if ($save_stmt) {
    $save_stmt->bind_param('ss', $purchase_id, $eticket_token);
    
    if ($save_stmt->execute()) {
        // Return success as plain text
        echo 'SUCCESS:' . $eticket_token;
    } else {
        echo 'Error saving e-ticket: ' . $save_stmt->error;
    }
} else {
    echo 'Prepare error: ' . $conn->error;
}
