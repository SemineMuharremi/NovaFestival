<?php
include '../config/db.php';
include '../config/auth.php';

require_admin_api();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'] ?? null;
    $event_date = $_POST['event_date'] ?? null;
    $description = $_POST['description'] ?? null;
    $artist_ids = json_decode($_POST['artist_ids'] ?? '[]', true);

    if (!$event_name || !$event_date) {
        echo json_encode(['success' => false, 'message' => 'Event name and date are required']);
        exit;
    }

    // Add event
    $add_query = "INSERT INTO events (event_name, event_date, description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($add_query);
    $stmt->bind_param('sss', $event_name, $event_date, $description);

    if ($stmt->execute()) {
        $event_id = $conn->insert_id;

        // Add artists
        if (!empty($artist_ids)) {
            $insert_artist_query = "INSERT INTO event_artists (event_id, artist_id) VALUES (?, ?)";
            $artist_stmt = $conn->prepare($insert_artist_query);

            foreach ($artist_ids as $artist_id) {
                $artist_stmt->bind_param('ii', $event_id, $artist_id);
                $artist_stmt->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Event added successfully', 'event_id' => $event_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding event: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
