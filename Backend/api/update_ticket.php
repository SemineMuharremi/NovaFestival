<?php
include '../config/db.php';
include '../config/auth.php';

require_admin_api();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'] ?? null;
    $ticket_name = $_POST['ticket_name'] ?? null;
    $price = $_POST['price'] ?? null;
    $availability = $_POST['availability'] ?? null;

    if (!$ticket_id || !$ticket_name || !$price || !$availability) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    $ticket_id = intval($ticket_id);
    $ticket_name = trim($ticket_name);
    $price = floatval($price);
    $availability = intval($availability);

    // Update ticket
    $update_query = "UPDATE ticket_types SET ticket_name = ?, price = ?, availability = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('sdii', $ticket_name, $price, $availability, $ticket_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ticket updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating ticket: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
