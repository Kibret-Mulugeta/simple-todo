<?php
// api/db.php — PDO MySQL connection for XAMPP local development

$DB_HOST = '127.0.0.1';
$DB_NAME = 'simple_todo';
$DB_USER = 'root';
$DB_PASS = ''; // default XAMPP root password is empty. Put password here if set.

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]);
    exit;
}
