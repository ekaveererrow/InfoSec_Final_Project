<?php
    
    $dsn = "mysql:host=localhost;dbname=build_stock_db";
    $db_username = "root";
    $db_password = "";

    try {
        $pdo = new PDO($dsn, $db_username, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // echo "Database Connected Successfully";
    } catch (PDOException $e) {
        error_log("Database Connection Failed: " . $e->getMessage()); // Log error
        die("Database connection error. Please try again later."); // Hide real error
    }