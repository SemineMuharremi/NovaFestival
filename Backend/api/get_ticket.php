<?php
include '../config/db.php';
include '../config/auth.php';

require_admin_api();

header('Content-Type: application/json');

$ticket_id = $_GET['id'] ?? null;

if (!$ticket_id) {
    echo json_encode(['success' => false, 'message' => 'Ticket ID is required']);
    exit;
}

$ticket_id = intval($ticket_id);

// Get ticket data
$ticket_query = "SELECT id, ticket_name, price, availability FROM ticket_types WHERE id = ?";
$stmt = $conn->prepare($ticket_query);
$stmt->bind_param('i', $ticket_id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

if (!$ticket) {
    echo json_encode(['success' => false, 'message' => 'Ticket not found']);
    exit;
}

echo json_encode([
    'success' => true,
    'ticket' => $ticket
]);
?>
