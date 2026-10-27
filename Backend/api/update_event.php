<?php
include '../config/db.php';
include '../config/auth.php';

require_admin_api();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;
    $event_name = $_POST['event_name'] ?? null;
    $event_date = $_POST['event_date'] ?? null;
    $description = $_POST['description'] ?? null;
    $artist_ids = json_decode($_POST['artist_ids'] ?? '[]', true);

    if (!$event_id || !$event_name || !$event_date) {
        echo json_encode(['success' => false, 'message' => 'Event ID, name and date are required']);
        exit;
    }

    // Update event
    $update_query = "UPDATE events SET event_name = ?, event_date = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('sssi', $event_name, $event_date, $description, $event_id);

    if ($stmt->execute()) {
        // Delete existing artists
        $delete_artists_query = "DELETE FROM event_artists WHERE event_id = ?";
        $delete_stmt = $conn->prepare($delete_artists_query);
        $delete_stmt->bind_param('i', $event_id);
        $delete_stmt->execute();

        // Add new artists
        if (!empty($artist_ids)) {
            $insert_artist_query = "INSERT INTO event_artists (event_id, artist_id) VALUES (?, ?)";
            $artist_stmt = $conn->prepare($insert_artist_query);

            foreach ($artist_ids as $artist_id) {
                $artist_stmt->bind_param('ii', $event_id, $artist_id);
                $artist_stmt->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating event: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
