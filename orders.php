<?php 

    require_once "./handlers/config.php";
    require_once "./handlers/database_handler.php";

    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
    }

    // Fetch Suppliers from the database
    try {
        $stmt = $pdo->prepare("SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name;");
        $stmt->execute();
        $supplierList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $supplierList = [];
        die("Error fetching supplier data. Please try again later.");
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
        // Get supplier_id from form
        $supplier_id = filter_input(INPUT_POST, 'supplier_id', FILTER_VALIDATE_INT);

        // Sanitize and validate inputs
        $product = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $orderValue = filter_input(INPUT_POST, 'orderValue', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $expiryDate = filter_input(INPUT_POST, 'expiryDate', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $expectedDelivery = filter_input(INPUT_POST, 'expectedDelivery', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        try {
            // Fetch supplier's contact and email
            $stmt = $pdo->prepare("SELECT supplier_name, contact, email FROM suppliers WHERE supplier_id = :supplier_id");
            $stmt->execute([':supplier_id' => $supplier_id]);
            $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$supplier) {
                header("Location: orders.php?add_order_error=supplier_not_found");
                exit();
            }
    
            // Assign supplier's contact and email to the order
            $supplier_name = $supplier["supplier_name"];
            $contact = $supplier["contact"];
            $email = $supplier["email"];
    
            // Ensure required fields are not empty
            if (!empty($product) && !empty($orderValue) && !empty($quantity) && !empty($expiryDate) && !empty($expectedDelivery)) {
                // Insert the new order into the orders table
                $stmt = $pdo->prepare("INSERT INTO orders (supplier_id, supplier_name, material_name, order_value, contact, email, quantity, expiry_date, expected_delivery)
                                    VALUES (:supplier_id, :supplier_name, :material_name, :order_value, :contact, :email, :quantity, :expiry_date, :expected_delivery);");
                $stmt->execute([
                    ":supplier_id" => $supplier_id,
                    ":supplier_name" => $supplier_name,
                    ":material_name" => $product,
                    ":order_value" => $orderValue,
                    ":contact" => $contact, // Auto-filled from supplier
                    ":email" => $email, // Auto-filled from supplier
                    ":quantity" => $quantity,
                    ":expiry_date" => $expiryDate,
                    ":expected_delivery" => $expectedDelivery
                ]);
    
                header("Location: orders.php?add_order_success=1");
                exit();
            } else {
                header("Location: orders.php?add_order_error=missing_fields");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            die("Something went wrong. Please try again later.");
        }
    }

    try {
        // Fetch all orders from the database
        $stmt = $pdo->prepare("SELECT supplier_name, material_name, order_value, quantity, order_id, expected_delivery, status FROM orders ORDER BY expected_delivery DESC");
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        die("Error fetching orders data. Please try again later.");
    }

    try {
        // Count total number of orders
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total_orders FROM orders;");
        $stmt->execute();
        $total_orders = $stmt->fetch(PDO::FETCH_ASSOC)["total_orders"];

        // Count total quantity of received orders
        $stmt = $pdo->prepare("SELECT SUM(quantity) AS total_quantity FROM orders WHERE status = 'Received';");
        $stmt->execute();
        $total_received = $stmt->fetch(PDO::FETCH_ASSOC)["total_quantity"] ?? 0;

        // Count total quantity of pending orders
        $stmt = $pdo->prepare("SELECT SUM(quantity) AS total_quantity FROM orders WHERE status = 'Pending';");
        $stmt->execute();
        $total_quantity = $stmt->fetch(PDO::FETCH_ASSOC)["total_quantity"] ?? 0;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $total_orders = 0;
        $total_received = 0;
        $total_quantity = 0;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Orders Page</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <link rel="stylesheet" href="./styles/style.css">
    
    <script defer src="./scripts/script.js"></script>
</head>
<body>
    <div class="container">
    <?php include "sidebar.php";?>
        <main>
            <header><?php include "header.php";?></header>
            <section class="orders">
                <div class="stat-container">
                    <div class="stat-box">
                        <div class="content">
                            <h3><?php echo htmlspecialchars($total_orders); ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    <div class="stat-box">
                        <div class="content">
                            <h3><?php echo htmlspecialchars($total_received); ?></h3>
                            <p>Total Received</p>
                        </div>
                    </div>
                    <div class="stat-box">
                        <div class="content">
                            <h3><?php echo htmlspecialchars($total_quantity); ?></h3>
                            <p>On the way</p>
                        </div>
                    </div>
                </div>
            </section><br>

            <div class="inventory">
                <div class="inventory-header">
                    <h1>Orders</h1>
                    <?php if ($_SESSION["role"] !== "procurement") { ?>
                        <button class="btn" id="addOrderBtn">Add Order</button>
                    <?php } ?>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Product</th>
                            <th>Order Value</th>
                            <th>Quantity</th>
                            <th>Order ID</th>
                            <th>Expected Delivery</th>
                            <th>Status</th>
                            <?php if ($_SESSION["role"] === "procurement") { ?>
                                <th>Action</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="ordersTable">
                        <?php foreach ($orders as $order) { ?>
                            <tr>
                                <td><?= htmlspecialchars($order["supplier_name"]); ?></td>
                                <td><?= htmlspecialchars($order["material_name"]); ?></td>
                                <td>PHP <?= htmlspecialchars($order["order_value"]); ?></td>
                                <td><?= htmlspecialchars($order["quantity"]); ?></td>
                                <td><?= htmlspecialchars($order["order_id"]); ?></td>
                                <td><?= htmlspecialchars($order["expected_delivery"]); ?></td>
                                <td class="<?= ($order["status"] === "Received") ? 'success' : 'warning'; ?>">
                                    <?= htmlspecialchars($order["status"]); ?>
                                </td>
                                <?php if ($_SESSION["role"] === "procurement") { ?>
                                    <td>
                                        <form action="./handlers/update_order.php" method="POST">
                                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order["order_id"]); ?>">
                                            <button type="submit" class="receivedBtn" <?=  ($order["status"] === "Received") ? "disabled" : ""; ?>>Receive</button>
                                        </form>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Order Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <h2>Add New Order</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <label for="supplier_id">Supplier:</label>
                <select id="supplier_id" name="supplier_id" required>
                    <option value="" disabled selected>Select Supplier</option>
                    <?php foreach ($supplierList as $supplier) { ?>
                        <option value="<?php echo $supplier["supplier_id"]; ?>"><?php echo htmlspecialchars($supplier["supplier_name"]); ?></option>
                    <?php } ?>
                </select> 

                <label for="product">Product:</label>
                <input type="text" id="product" name="product" required>

                <label for="orderValue">Order Value:</label>
                <input type="number" id="orderValue" name="orderValue" min="0" value="0" required>

                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" required>

                <label for="expiryDate">Expiry:</label>
                <input type="date" id="expiryDate" name="expiryDate" required>

                <label for="expectedDelivery">Expected Delivery:</label>
                <input type="date" id="expectedDelivery" name="expectedDelivery" required>

                <div class="modal-buttons">
                    <button type="submit" class="btn">Place Order</button>
                    <button type="button" class="btn-close" id="closeModal">Close</button>
                </div>
            </form>
        </div>
    </div>

    <script src="./scripts/functions.js"></script>
</body>
</html>
