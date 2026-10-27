<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_admin() {
    return isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin';
}

// For JSON API endpoints: respond with 403 and stop if the user is not an admin
function require_admin_api() {
    if (!is_admin()) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

// For pages: redirect to the home page if the user is not an admin
function require_admin_page($redirect = 'index.php') {
    if (!is_admin()) {
        header("Location: $redirect");
        exit;
    }
}
