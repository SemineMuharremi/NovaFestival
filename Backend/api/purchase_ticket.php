<?php
session_start();
header('Content-Type: application/json');
include '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = $_SESSION['user_id'];
$ticket_type_id = intval($_POST['ticket_type_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';

// Valid payment methods that match database ENUM
$valid_methods = ['credit_card', 'debit_card', 'paypal', 'bank_transfer'];


// If no payment method provided
if (empty($payment_method)) {
    echo json_encode(['success' => false, 'message' => 'No payment method selected. Please add a payment method first.']);
    exit;
}

// Validate payment method
if (!in_array($payment_method, $valid_methods)) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment method. Please select a valid payment method.']);
    exit;
}

// Validate inputs
if ($ticket_type_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ticket or quantity information']);
    exit;
}

// Get ticket details
$ticket_query = "SELECT id, ticket_name, price, availability FROM ticket_types WHERE id = ?";
$ticket_stmt = $conn->prepare($ticket_query);
if (!$ticket_stmt) {
    echo json_encode(['success' => false, 'message' => 'Database prepare error: ' . $conn->error]);
    exit;
}

$ticket_stmt->bind_param('i', $ticket_type_id);
$ticket_stmt->execute();
$ticket_result = $ticket_stmt->get_result();
$ticket = $ticket_result->fetch_assoc();

if (!$ticket) {
    echo json_encode(['success' => false, 'message' => 'Ticket type not found (ID: ' . $ticket_type_id . ')']);
    exit;
}

// Check availability
if ($ticket['availability'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Not enough tickets available. Available: ' . $ticket['availability'] . ', Requested: ' . $quantity]);
    exit;
}

// Calculate total price
$unit_price = floatval($ticket['price']);
$total_price = $unit_price * $quantity;


// Start transaction
$conn->begin_transaction();

try {
    // 1. Insert purchase record
    $purchase_query = "INSERT INTO purchases (user_id, ticket_type_id, quantity, unit_price, total_price, status) 
                       VALUES (?, ?, ?, ?, ?, 'pending')";
    $purchase_stmt = $conn->prepare($purchase_query);
    if (!$purchase_stmt) {
        throw new Exception('Prepare error: ' . $conn->error);
    }
    
    $purchase_stmt->bind_param('iiidd', $user_id, $ticket_type_id, $quantity, $unit_price, $total_price);
    
    if (!$purchase_stmt->execute()) {
        throw new Exception('Error creating purchase: ' . $conn->error);
    }
    
    $purchase_id = $conn->insert_id;

    // 2. Insert payment record
    $transaction_id = 'TXN-' . time() . '-' . bin2hex(random_bytes(4));
    $payment_query = "INSERT INTO payments (purchase_id, payment_method, amount, payment_status, transaction_id) 
                      VALUES (?, ?, ?, 'completed', ?)";
    $payment_stmt = $conn->prepare($payment_query);
    if (!$payment_stmt) {
        throw new Exception('Payment prepare error: ' . $conn->error);
    }
    
    $payment_stmt->bind_param('isds', $purchase_id, $payment_method, $total_price, $transaction_id);
    
    if (!$payment_stmt->execute()) {
        throw new Exception('Error creating payment: ' . $payment_stmt->error);
    }
    

    // 3. Update ticket availability (atomic, so concurrent purchases cannot oversell)
    $update_ticket_query = "UPDATE ticket_types SET availability = availability - ? WHERE id = ? AND availability >= ?";
    $update_ticket_stmt = $conn->prepare($update_ticket_query);
    if (!$update_ticket_stmt) {
        throw new Exception('Update prepare error: ' . $conn->error);
    }

    $update_ticket_stmt->bind_param('iii', $quantity, $ticket_type_id, $quantity);

    if (!$update_ticket_stmt->execute()) {
        throw new Exception('Error updating availability: ' . $update_ticket_stmt->error);
    }

    if ($update_ticket_stmt->affected_rows === 0) {
        throw new Exception('Not enough tickets available');
    }

    // Commit transaction
    $conn->commit();

    // Return success WITHOUT e-ticket (pending admin confirmation)
    echo json_encode([
        'success' => true,
        'message' => 'Purchase pending admin confirmation',
        'purchase_id' => $purchase_id,
        'ticket_name' => $ticket['ticket_name'],
        'quantity' => $quantity,
        'total_price' => $total_price,
        'transaction_id' => $transaction_id,
        'status' => 'pending'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
