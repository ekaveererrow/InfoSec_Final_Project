<?php 

    require_once "./handlers/config.php";
    require_once "./handlers/database_handler.php";

    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
    }

    // Sum total stock quantity of all materials
    try {
        $stmt = $pdo->prepare("SELECT SUM(stock_quantity) AS total_items FROM materials");
        $stmt->execute();
        $totalItems = $stmt->fetch(PDO::FETCH_ASSOC)["total_items"] ?? 0;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        die("Error fetching supplier data. Please try again later.");
    }

    try {
        // Fetch total stock, low-stock count, and out-of-stock count
        $stmt = $pdo->prepare("
        SELECT 
            SUM(stock_quantity) AS total_items,
            SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) AS out_of_stock,
            SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= reorder_level THEN 1 ELSE 0 END) AS low_stock
        FROM materials
        ");
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalItems = $row["total_items"] ?? 0;
        $outOfStock = $row["out_of_stock"] ?? 0;
        $lowStock = $row["low_stock"] ?? 0;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        die("Error fetching supplier data. Please try again later.");
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

    // Fetch Materials from the database
    try {
        $stmt = $pdo->prepare("SELECT * FROM materials ORDER BY material_name;");
        $stmt->execute();
        $materialList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $materialList = [];
        die("Error fetching material data. Please try again later.");
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
                <h1>Overall Inventory</h1>
                <?php if ($_SESSION["role"] !== "procurement") { ?>
                    <button class="btn" id="openModalBtn">Add Product</button>
                    <button class="btn btn-outline" id="updateModalBtn">Update Stocks</button>
                <?php } ?>
            </div>
                <div class="inventory-stats">
                    <div><strong>Total Items</strong> <br><?php echo htmlspecialchars($totalItems); ?></div>
                    <div><strong>Low Stocks</strong> <br><?php echo htmlspecialchars($lowStock); ?></div>
                    <div><strong>Out of Stock</strong> <br><?php echo htmlspecialchars($outOfStock); ?></div>
                </div>


                <table>
                    <thead>
                        <tr>
                            <th>Products</th>
                            <th>Buying Price</th>
                            <th>Quantity</th>
                            <th>Threshold Value</th>
                            <th>Expiry Date</th>
                            <th>Availability</th>
                            <?php if ($_SESSION["role"] !== "procurement") { ?>
                                <th>Action</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($materialList as $material) { ?>
                            <tr>
                                <td><?= htmlspecialchars($material["material_name"]); ?></td>
                                <td>PHP <?= htmlspecialchars($material["buying_price"]); ?></td>
                                <td><?= htmlspecialchars($material["stock_quantity"]) . " " . htmlspecialchars($material["unit"]); ?></td>
                                <td><?= htmlspecialchars($material["reorder_level"]) . " " . htmlspecialchars($material["unit"]); ?></td>
                                <td><?= htmlspecialchars($material["expiry_date"]); ?></td>
                                <td class="<?= $material['availability'] === 'In-Stock' ? 'success' : ($material['availability'] === 'Low Stock' ? 'warning' : 'danger') ?>">
                                    <?= htmlspecialchars($material['availability']) ?>
                                </td>
                            
                                <?php if ($_SESSION["role"] !== "procurement") { ?>
                                    <td>
                                        <form method="POST" action="./handlers/remove_material.php" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this material?');">
                                            <input type="hidden" name="material_id" value="<?= htmlspecialchars($material['material_id']); ?>">
                                            <button type="submit">❌</button>
                                        </form>
                                    </td>
                                <?php } ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </section>
        </main>


        <!-- Modal Structure -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <h2>Add Product</h2>
                <form action="./handlers/add_materials.php" method="POST">

                    <label for="supplierName">Supplier:</label>
                    <select id="supplierName" name="supplierName" required>
                        <option value="" disabled selected>Select Supplier</option>
                        <?php foreach ($supplierList as $supplier) { ?>
                            <option value="<?php echo $supplier["supplier_id"]; ?>"><?php echo htmlspecialchars($supplier["supplier_name"]); ?></option>
                        <?php } ?>
                    </select> 

                    <label for="productName">Product:</label>
                    <input type="text" id="productName" name="productName" required>

                    <label for="buyingPrice">Price:</label>
                    <input type="number" id="buyingPrice" name="buyingPrice" min="0" required>

                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="0" required>

                    <label for="unit">Unit:</label>
                    <select id="unit" name="unit" required>
                        <option value="kg">kg</option>
                        <option value="pcs">pcs</option>
                        <option value="liters">liters</option>
                    </select>

                    <label for="expiryDate">Expiry:</label>
                    <input type="date" id="expiryDate" name="expiryDate" required>
                    
                    <div class="modal-buttons">
                    <button type="submit" class="btn">Add</button>
                    <button type="button" class="btn-close" id="closeModalBtn">Close</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Update Modal -->
        <div id="updateStockModal" class="modal">
            <div class="modal-content">
                <h2>Update Stock</h2>
                <form  action="./handlers/update_materials.php" method="POST">
                    <label for="product">Product:</label>
                    <select name="product" id="product" required>
                        <option value="" disabled selected>Select Product</option>
                        <?php foreach ($materialList as $material) { ?>
                            <option value="<?php echo $material["material_id"]; ?>"><?php echo htmlspecialchars($material["material_name"]); ?></option>
                        <?php } ?>
                    </select>

                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="0" placeholder="Enter quantity" value="1" required>

                    <div class="modal-buttons">
                        <button id="addStockUpdate" class="btn-add">Update</button>
                        <button id="closeModal" class="btn btn-outline">Close</button>
                    </div>
                </form>
            </div>
        </div>

    <script src="./scripts/functions.js"></script>
    <script src="./scripts/clean_url.js"></script>
</body>
</html>
