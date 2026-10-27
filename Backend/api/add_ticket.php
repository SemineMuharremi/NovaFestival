<?php
include '../config/db.php';
include '../config/auth.php';

require_admin_api();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_name = $_POST['ticket_name'] ?? null;
    $price = $_POST['price'] ?? null;
    $availability = $_POST['availability'] ?? null;

    if (!$ticket_name || !$price || !$availability) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    $ticket_name = trim($ticket_name);
    $price = floatval($price);
    $availability = intval($availability);

    // Insert new ticket
    $insert_query = "INSERT INTO ticket_types (ticket_name, price, availability) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('sdi', $ticket_name, $price, $availability);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ticket added successfully', 'ticket_id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding ticket: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
