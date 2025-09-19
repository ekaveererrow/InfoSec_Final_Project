<?php 

    require_once "./handlers/config.php";
    require_once "./handlers/database_handler.php";

    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
    }
    
    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
        $first_name = filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_SPECIAL_CHARS);
        $last_name = filter_input(INPUT_POST, "last_name", FILTER_SANITIZE_SPECIAL_CHARS);
        $position = filter_input(INPUT_POST, "position", FILTER_SANITIZE_SPECIAL_CHARS);
        $contact = filter_input(INPUT_POST, "contact", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);

        // Check for empty fields
        if (empty($first_name) || empty($last_name) || empty($position) || empty($contact) || empty($email)) {
            header("Location: personnel.php?add_personnel_error=1");
            exit();
        } else {
            try {
                // Insert personnel into the database
                $stmt = $pdo->prepare("INSERT INTO personnel (first_name, last_name, position, contact, email) VALUES (:first_name, :last_name, :position, :contact, :email);");
                $stmt->execute([
                    ":first_name" => $first_name,
                    ":last_name" => $last_name,
                    ":position" => $position,
                    ":contact" => $contact,
                    ":email" => $email
                ]);
    
                // Redirect to prevent form resubmission
                header("Location: personnel.php?add_personnel_success=1");
                exit();
                
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                die("Something went wrong. Please try again later.");
            }
        }
    }

    try {
        // Fetch all personnel from the database
        $stmt = $pdo->query("SELECT * FROM personnel ORDER BY created_at DESC");
        $personnelList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $personnelList = []; // Ensure the variable is still set to avoid undefined errors
        die("Error fetching personnel data. Please try again later.");
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnel</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <link rel="stylesheet" href="./styles/style.css">
    <script defer src="./scripts/functions.js"></script>
</head>
<body>
    <div class="container">
        <?php include "sidebar.php"; ?>
        <main>
            <header><?php include "header.php"; ?></header>
            <section class="inventory">
                <div class="inventory-header">
                    <h1>Personnel</h1>
                    <?php if ($_SESSION["role"] !== "procurement") { ?>
                        <button class="btn" id="openFormBtn">Add Personnel</button>
                    <?php } ?>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Position</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Status</th>
                            <?php if ($_SESSION["role"] !== "procurement") { ?>
                                <th>Action</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="personnelTable">
                        <?php foreach ($personnelList as $personnel) : ?>
                            <tr>
                                <td><?= htmlspecialchars($personnel["first_name"]); ?></td>
                                <td><?= htmlspecialchars($personnel["last_name"]); ?></td>
                                <td><?= htmlspecialchars($personnel["position"]); ?></td>
                                <td><?= htmlspecialchars($personnel["contact"]); ?></td>
                                <td><?= htmlspecialchars($personnel["email"]); ?></td>
                                <td class="status success"><?= htmlspecialchars($personnel["status"]); ?></td>
                                <?php if ($_SESSION["role"] !== "procurement") { ?>
                                    <td><button class="btn btn-outline toggle-status">Mark Absent</button></td>
                                <?php } ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- Modal for Adding Personnel -->
    <div id="personnelFormModal" class="modal">
        <div class="modal-content">
            <h2>Add Personnel</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>

                <label for="position">Position:</label>
                <input type="text" id="position" name="position" required>

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
    <script src="./scripts/clean_url.js"></script>
</body>
</html>
