<?php 
require_once "./handlers/config.php";
require_once "./handlers/database_handler.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

try {
    // Count total personnel
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_personnel FROM personnel;");
    $stmt->execute();
    $personnelCount = $stmt->fetch(PDO::FETCH_ASSOC)["total_personnel"];

    // Count total number of suppliers
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_suppliers FROM suppliers;");
    $stmt->execute();
    $totalSuppliers = $stmt->fetch(PDO::FETCH_ASSOC)["total_suppliers"];

    // Sum total stock quantity of all materials
    $stmt = $pdo->prepare("SELECT SUM(stock_quantity) AS total_items FROM materials;");
    $stmt->execute();
    $totalItems = $stmt->fetch(PDO::FETCH_ASSOC)["total_items"] ?? 0;

    // Count total number of orders
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_orders FROM orders;");
    $stmt->execute();
    $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)["total_orders"];

    // Count pending orders
    $stmt = $pdo->prepare("SELECT COUNT(*) AS pending_orders FROM orders WHERE status = 'Pending';");
    $stmt->execute();
    $pendingOrders = $stmt->fetch(PDO::FETCH_ASSOC)["pending_orders"];

    // Count received orders
    $stmt = $pdo->prepare("SELECT COUNT(*) AS received_orders FROM orders WHERE status = 'Received';");
    $stmt->execute();
    $receivedOrders = $stmt->fetch(PDO::FETCH_ASSOC)["received_orders"];

    // Get materials with low stock (less than 10)
    $stmt = $pdo->prepare("SELECT material_name, stock_quantity FROM materials WHERE stock_quantity < 10 ORDER BY stock_quantity ASC LIMIT 5;");
    $stmt->execute();
    $lowStockItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $personnelCount = 0;
    $totalSuppliers = 0;
    $totalItems = 0;
    $totalOrders = 0;
    $pendingOrders = 0;
    $receivedOrders = 0;
    $lowStockItems = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <link rel="stylesheet" href="./styles/style.css">
    <script defer src="./scripts/script.js"></script>

    <style>
        .dashboard-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 20px;
        }
        .stat-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: space-between;
        }
        .stat-box {
            flex: 1 1 calc(25% - 1rem);
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s ease;
        }
        .stat-box:hover {
            transform: scale(1.03);
        }
        .stat-box .icon {
            font-size: 2rem;
        }
        .low-stock {
            margin-top: 30px;
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .low-stock h3 {
            margin-bottom: 10px;
        }
        .low-stock table {
            width: 100%;
            border-collapse: collapse;
        }
        .low-stock table th, .low-stock table td {
            text-align: left;
            padding: 8px;
        }
        .low-stock table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<div class="container">
    <?php include "sidebar.php"; ?>
    <main>
        <header><?php include "header.php"; ?></header>

        <h1>Dashboard</h1>

        <section class="dashboard-stats">
            <div class="stat-container">
                <div class="stat-box">
                    <div class="icon">📦</div>
                    <div class="content">
                        <h3><?= htmlspecialchars($totalItems); ?></h3>
                        <p>Total Items</p>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="icon">👷</div>
                    <div class="content">
                        <h3><?= htmlspecialchars($personnelCount); ?></h3>
                        <p>Personnel</p>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="icon">🏭</div>
                    <div class="content">
                        <h3><?= htmlspecialchars($totalSuppliers); ?></h3>
                        <p>Suppliers</p>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="icon">🧾</div>
                    <div class="content">
                        <h3><?= htmlspecialchars($totalOrders); ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="icon">⏳</div>
                    <div class="content">
                        <h3><?= htmlspecialchars($pendingOrders); ?></h3>
                        <p>Pending Orders</p>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="icon">✅</div>
                    <div class="content">
                        <h3><?= htmlspecialchars($receivedOrders); ?></h3>
                        <p>Received Orders</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Low Stock Section -->
        <section class="low-stock">
            <h3>⚠️ Low Stock Materials</h3>
            <?php if (count($lowStockItems) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Material Name</th>
                            <th>Quantity Left</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item["material_name"]); ?></td>
                                <td><?= htmlspecialchars($item["stock_quantity"]); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>All materials are sufficiently stocked.</p>
            <?php endif; ?>
        </section>

    </main>
</div>
</body>
</html>
                