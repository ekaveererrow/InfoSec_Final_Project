<?php

    require_once "config.php";
    require_once "database_handler.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {

        if (!isset($_SESSION["user_id"])) {
            die("Unauthorized access.");
        }

        // Validate and sanitize input
        $materialId = filter_input(INPUT_POST, "material_id", FILTER_VALIDATE_INT);

        if (!$materialId) {
            die("Invalid request.");
        }

        try {
            // Prepare the delete statement
            $stmt = $pdo->prepare("DELETE FROM materials WHERE material_id = :material_id");
            $stmt->execute([":material_id" => $materialId]);

            // Redirect back to inventory page after deletion
            header("Location: ../inventory.php?remove_material_success=1");
            exit();
        } catch (PDOException $e) {
            error_log("Delete material error: " . $e->getMessage());
            header("Location: ../inventory.php?remove_material_error=1");
            exit();
        }
    } else {
        header("Location: ../index.php");
        die();
    }