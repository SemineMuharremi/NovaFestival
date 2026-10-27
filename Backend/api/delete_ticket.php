<?php
include '../config/db.php';
include '../config/auth.php';

require_admin_api();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'] ?? null;

    if (!$ticket_id) {
        echo json_encode(['success' => false, 'message' => 'Ticket ID is required']);
        exit;
    }

    $ticket_id = intval($ticket_id);

    // Delete ticket
    $delete_query = "DELETE FROM ticket_types WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $ticket_id);

    try {
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Ticket deleted successfully']);
    } catch (mysqli_sql_exception $e) {
        // Foreign key violation: this ticket type already has purchases
        $message = $e->getCode() === 1451
            ? 'This ticket type has purchases and cannot be deleted'
            : 'Error deleting ticket';
        echo json_encode(['success' => false, 'message' => $message]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
