<?php
include '../config/db.php';
include '../config/auth.php';

require_admin_api();

header('Content-Type: application/json');

$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    echo json_encode(['success' => false, 'message' => 'Event ID is required']);
    exit;
}

// Get event data
$event_query = "SELECT id, event_name, event_date, description FROM events WHERE id = ?";
$stmt = $conn->prepare($event_query);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo json_encode(['success' => false, 'message' => 'Event not found']);
    exit;
}

// Get artist IDs for this event
$artists_query = "SELECT artist_id FROM event_artists WHERE event_id = ?";
$artists_stmt = $conn->prepare($artists_query);
$artists_stmt->bind_param('i', $event_id);
$artists_stmt->execute();
$artists_result = $artists_stmt->get_result();
$artist_ids = array();
while ($row = $artists_result->fetch_assoc()) {
    $artist_ids[] = (int)$row['artist_id'];
}

echo json_encode([
    'success' => true,
    'event' => $event,
    'artist_ids' => $artist_ids
]);
?>
