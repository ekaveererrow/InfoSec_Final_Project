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
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $personnelCount = 0;
        $totalSuppliers = 0;
        $totalItems = 0;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <link rel="stylesheet" href="./styles/style.css">

    <script defer src="./scripts/script.js"></script>

    <title>Dashboard</title>
</head>
<body>
    <div class="container">
        <?php include "sidebar.php";?>
        <main>
        <header><?php include "header.php"; ?> </header>

            <main>
                <h1>Dashboard</h1>
                
                <section class="dashboard-stats">
                    <div class="stat-container">
                        <div class="stat-box">
                            <div class="icon">📦</div>
                            <div class="content">
                                <h3><?php echo htmlspecialchars($totalItems); ?></h3>
                                <p>Total items</p>
                            </div>
                        </div>
                    
                        <div class="stat-box">
                            <div class="icon">👥</div>
                            <div class="content">
                                <h3><?php echo htmlspecialchars($personnelCount); ?></h3>
                                <p>Personnel</p>
                            </div>
                        </div>
                    
                        <div class="stat-box">
                            <div class="icon">📦</div>
                            <div class="content">
                                <h3><?php echo htmlspecialchars($totalSuppliers); ?></h3>
                                <p>Suppliers</p>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    <script src="./scripts/script.js"></script>
</body>
</html>