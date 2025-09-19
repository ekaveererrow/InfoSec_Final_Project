<?php

    require_once "config.php";
    require_once "database_handler.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
        // Sanitize input data
        $material_name = filter_input(INPUT_POST, "productName", FILTER_SANITIZE_SPECIAL_CHARS);
        $buying_price = filter_input(INPUT_POST, "buyingPrice", FILTER_VALIDATE_FLOAT);
        $unit = filter_input(INPUT_POST, "unit", FILTER_SANITIZE_SPECIAL_CHARS);
        $stock_quantity = filter_input(INPUT_POST, "quantity", FILTER_VALIDATE_INT);
        $expiry_date = filter_input(INPUT_POST, "expiryDate", FILTER_SANITIZE_SPECIAL_CHARS);
        $supplier_id = filter_input(INPUT_POST, "supplierName", FILTER_VALIDATE_INT);

        // Validate inputs
        if (!$material_name || !$buying_price || !$unit || !$stock_quantity || !$expiry_date || !$supplier_id) {
            die("Invalid input! Please fill in all fields correctly.");
        }

        // Determine availability status
        if ($stock_quantity == 0) {
            $availability = "Out of Stock";
        } elseif ($stock_quantity <= $reorder_level) {
            $availability = "Low Stock";
        } else {
            $availability = "In-Stock";
        }

        $reorder_level = $stock_quantity / 2;

        try {
            // Insert data into the database
            $stmt = $pdo->prepare("INSERT INTO materials (material_name, buying_price, unit, stock_quantity, reorder_level, expiry_date, supplier_id, availability) 
            VALUES (:material_name, :buying_price, :unit, :stock_quantity, :reorder_level, :expiry_date, :supplier_id, :availability)");

            $stmt->execute([
                ":material_name" => $material_name,
                ":buying_price" => $buying_price,
                ":unit" => $unit,
                ":stock_quantity" => $stock_quantity,
                ":reorder_level" => $reorder_level,
                ":expiry_date" => $expiry_date,
                ":supplier_id" => $supplier_id,
                ":availability" => $availability
            ]);

            // Redirect back to inventory page
            header("Location: ../inventory.php?add_material_success=1");
            exit();

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            die("Something went wrong. Please try again later.");
        }
    } else {
        header("Location: ../index.php");
        die();
    }