<?php

// Database settings can be overridden with environment variables.
// The defaults match a standard local XAMPP installation.
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'festival';
$db_port = (int) (getenv('DB_PORT') ?: 3306);

try {
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    mysqli_set_charset($conn, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die("Database connection failed");
}
