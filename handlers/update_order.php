<?php

    require_once "config.php";
    require_once "database_handler.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["role"]) && $_SESSION["role"] === "procurement") {
        $order_id = filter_input(INPUT_POST, "order_id", FILTER_VALIDATE_INT);

        if ($order_id) {
            try {
                $pdo->beginTransaction();

                // Get order details
                $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id;");
                $stmt->execute([":order_id" => $order_id]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($order) {
                    $supplier_id = $order["supplier_id"];
                    $material_name = $order["material_name"];
                    $order_value = $order["order_value"];
                    $contact = $order["contact"];
                    $email = $order["email"];
                    $quantity = $order["quantity"];
                    $expiry_date = $order["expiry_date"];
                    $expected_delivery = $order["expected_delivery"];

                    // Update order status to Received
                    $stmt = $pdo->prepare("UPDATE orders SET status = 'received' WHERE order_id = :order_id");
                    $stmt->execute([":order_id" => $order_id]);

                    $reorder_level = $quantity / 2;

                    // Add product to materials table
                    $stmt = $pdo->prepare("INSERT INTO materials (material_name, buying_price, unit, stock_quantity, reorder_level, expiry_date, supplier_id) 
                                            VALUES (:material_name, :buying_price, :unit, :stock_quantity, :reorder_level, :expiry_date, :supplier_id);");
                    $stmt->execute([
                        ":material_name" => $material_name,
                        ":buying_price" => $order_value,
                        ":unit" => ".",
                        ":stock_quantity" => $quantity,
                        ":reorder_level" => $reorder_level,
                        ":expiry_date" => $expiry_date,
                        ":supplier_id" => $supplier_id
                    ]);

                    $pdo->commit();
                    header("Location: ../orders.php?order_received_success=1");
                    exit();
                } else {
                    throw new Exception("Order not found.");
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log("Database error: " . $e->getMessage());
                die("Something went wrong. Please try again later.");
            }
        }
    }