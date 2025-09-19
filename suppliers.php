<?php 

    require_once "./handlers/config.php";
    require_once "./handlers/database_handler.php";

    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["role"]) && $_SESSION["role"] === "procurement") {
        $supplier_name = filter_input(INPUT_POST, "supplier_name", FILTER_SANITIZE_SPECIAL_CHARS);
        $product = filter_input(INPUT_POST, "product", FILTER_SANITIZE_SPECIAL_CHARS);
        $contact = filter_input(INPUT_POST, "contact", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);

        // Validate contact number (PH format: 11 digits, starts with 09)
        if (!preg_match("/^09\d{9}$/", $contact)) {
            header("Location: suppliers.php?add_supplier_error=2");
            exit();
        }

        // Check for empty fields
        if (!empty($supplier_name) && !empty($product) && !empty($contact) && !empty($email)) {
            try {
                // Insert supplier into the database
                $stmt = $pdo->prepare("INSERT INTO suppliers (supplier_name, product, contact, email) VALUES (:supplier_name, :product, :contact, :email);");
                $stmt->execute([
                    ":supplier_name" => $supplier_name,
                    ":product" => $product,
                    ":contact" => $contact,
                    ":email" => $email,
                ]);

                // Redirect to prevent form resubmission
                header("Location: suppliers.php?add_supplier_success=1");
                exit();
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                die("Something went wrong. Please try again later.");
            }
        } else {
            header("Location: suppliers.php?add_supplier_error=1");
            exit();
        }
    }

    // Fetch suppliers
    try {
        // Fetch all suppliers from the database
        $stmt = $pdo->query("SELECT * FROM suppliers ORDER BY supplier_id DESC");
        $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $suppliers = [];
        die("Error fetching supplier data. Please try again later.");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inventory</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <link rel="stylesheet" href="./styles/style.css">

    <script defer src="./scripts/script.js"></script>
</head>
<body>
    <div class="container">
    <?php include "sidebar.php";?>
        <main>
            <header><?php include "header.php";?></header>
            <section class="inventory">
                <div class="inventory-header">
                    <h1>Supplier</h1>
                    <?php if ($_SESSION["role"] === "procurement") { ?>
                        <button class="btn" id="openFormBtn">Add Supplier</button>
                    <?php } ?>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Product</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <?php if ($_SESSION["role"] === "procurement") { ?>
                                <th>Action</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="supplierTable">
                        <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td><?= htmlspecialchars($supplier['supplier_name']); ?></td>
                            <td><?= htmlspecialchars($supplier['product']); ?></td>
                            <td><?= htmlspecialchars($supplier['contact']); ?></td>
                            <td><?= htmlspecialchars($supplier['email']); ?></td>
                            <?php if ($_SESSION["role"] === "procurement") { ?>
                                <td>
                                    <form method="POST" action="./handlers/remove_supplier.php" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this material?');">
                                        <input type="hidden" name="supplier_id" value="<?= htmlspecialchars($supplier['supplier_id']); ?>">
                                        <button type="submit">❌</button>
                                    </form>
                                </td>
                            <?php } ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- Modal for Adding Personnel -->
    <div id="supplierFormModal" class="modal">
        <div class="modal-content">
            <h2>Add Supplier</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="supplier_name" required>

                <label for="position">Product:</label>
                <input type="text" id="product" name="product" required>

                <label for="contact">Contact:</label>
                <input type="text" id="contact" name="contact" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <div class="modal-buttons">
                    <button type="submit" class="btn">Add</button>
                    <button type="button" class="btn-close" id="closeFormBtn">Close</button>
                </div>
            </form>
        </div>
    </div>

    <script src="./scripts/functions.js"></script>
    <script src="./scripts/clean_url.js"></script>
</body>
</html>
