<?php
include '../config/db.php';
include '../config/auth.php';

require_admin_api();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;

    if (!$event_id) {
        echo json_encode(['success' => false, 'message' => 'Event ID is required']);
        exit;
    }

    // Delete event artists first
    $delete_artists_query = "DELETE FROM event_artists WHERE event_id = ?";
    $delete_artists_stmt = $conn->prepare($delete_artists_query);
    $delete_artists_stmt->bind_param('i', $event_id);
    $delete_artists_stmt->execute();

    // Delete event
    $delete_query = "DELETE FROM events WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $event_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting event: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
