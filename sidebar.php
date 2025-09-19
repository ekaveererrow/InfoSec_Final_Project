<?php 

    require_once "./handlers/config.php";

    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
    }

?>

<aside>
    <div class="logo">
        <h2>Build Stock</h2>
    </div>
    <nav class="sidebar">
        <a href="dashboard.php" id="dashboard-link"><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
        <a href="personnel.php" id="inventory-link"><span class="material-symbols-outlined">account_circle</span>Users</a>
        <a href="inventory.php" id="inventory-link"><span class="material-symbols-outlined">inventory</span>Inventory</a>
        <a href="suppliers.php"><span class="material-symbols-outlined">engineering</span>Suppliers</a>
        <a href="orders.php"><span class="material-symbols-outlined">shopping_cart</span>Orders</a>
        <a href="./handlers/logout_handler.php" class="logout"><span class="material-symbols-outlined">logout</span>Log Out</a>
    </nav>
</aside>
