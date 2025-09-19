<?php

    require_once "config.php";
    require_once "database_handler.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
        if (!isset($_SESSION["user_id"])) {
            die("Unauthorized access.");
        }

        $materialId = filter_input(INPUT_POST, "product", FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, "quantity", FILTER_VALIDATE_INT);

        if (!$materialId || !$quantity || $quantity <= 0) {
            die("Invalid input.");
        }

        try {
            // Fetch current stock data
            $query = $pdo->prepare("SELECT stock_quantity, reorder_level FROM materials WHERE material_id = :materialId");
            $query->execute([":materialId" => $materialId]);
            $materialData = $query->fetch(PDO::FETCH_ASSOC);

            if (!$materialData) {
                die("Material not found.");
            }

            // Update stock quantity first
            $stmt = $pdo->prepare("UPDATE materials SET stock_quantity = :quantity WHERE material_id = :product_id");
            $stmt->execute([
                ":quantity" => $quantity,
                ":product_id" => $materialId
            ]);

            // Re-fetch updated stock quantity
            $query = $pdo->prepare("SELECT stock_quantity, reorder_level FROM materials WHERE material_id = :materialId");
            $query->execute([":materialId" => $materialId]);
            $updatedMaterial = $query->fetch(PDO::FETCH_ASSOC);

            // Now determine availability based on updated stock
            if ($updatedMaterial["stock_quantity"] == 0) {
                $availability = "Out of Stock";
            } elseif ($updatedMaterial["stock_quantity"] <= $updatedMaterial["reorder_level"]) {
                $availability = "Low Stock";
            } else {
                $availability = "In-Stock";
            }

            // Update availability
            $stmt = $pdo->prepare("UPDATE materials SET availability = :availability WHERE material_id = :product_id");
            $stmt->execute([
                ":availability" => $availability,
                ":product_id" => $materialId
            ]);

            header("Location: ../inventory.php?update_material_success=1");
            exit();
        } catch (PDOException $e) {
            error_log("Stock update error: " . $e->getMessage());
            header("Location: ../inventory.php?update_material_error=1");
            die("Error updating stock. Please try again later.");
        }
    } else {
        header("Location: ../index.php");
        die();
    }