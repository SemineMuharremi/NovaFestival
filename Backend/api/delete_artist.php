<?php
include '../config/db.php';
include '../config/auth.php';

require_admin_api();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artist_id = $_POST['artist_id'] ?? null;

    if (!$artist_id) {
        echo json_encode(['success' => false, 'message' => 'Artist ID is required']);
        exit;
    }

    // Delete artist from event_artists table first (cascading delete)
    $delete_events_query = "DELETE FROM event_artists WHERE artist_id = ?";
    $delete_events_stmt = $conn->prepare($delete_events_query);
    $delete_events_stmt->bind_param('i', $artist_id);
    $delete_events_stmt->execute();

    // Delete artist
    $delete_query = "DELETE FROM artists WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $artist_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Artist deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting artist: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
