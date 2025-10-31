<?php
// ========================
// DATABASE CONNECTION
// ========================

$host = "localhost";
$db_name = "build-stockdb";
$db_username = "root";
$db_password = "";
$dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    // Connection successful (no echo for security)
} catch (PDOException $e) {
    error_log("Database Connection Failed: " . $e->getMessage());
    die("Database connection error. Please try again later.");
}
