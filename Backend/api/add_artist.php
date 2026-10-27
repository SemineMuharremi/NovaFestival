<?php
include '../config/db.php';
include '../config/auth.php';

require_admin_api();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artist_name = $_POST['artist_name'] ?? null;

    if (!$artist_name) {
        echo json_encode(['success' => false, 'message' => 'Artist name is required']);
        exit;
    }

    $artist_name = trim($artist_name);

    // Insert new artist
    $insert_query = "INSERT INTO artists (artist_name) VALUES (?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('s', $artist_name);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Artist added successfully', 'artist_id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding artist: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
