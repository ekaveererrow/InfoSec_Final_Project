<?php

    require_once "./handlers/config.php";
    require_once "./handlers/database_handler.php";

    // Generate CSRF Token if not set
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    // Function to validate password complexity
    function validatePassword($password) {
        // Password must be at least 8 characters long and contain at least one letter, one number, and one special character
        $regex = '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        return preg_match($regex, $password);
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
        $first_name = filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_SPECIAL_CHARS);
        $last_name = filter_input(INPUT_POST, "last_name", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
        $password = $_POST["password"];
        $role = filter_input(INPUT_POST, "role", FILTER_SANITIZE_SPECIAL_CHARS);

        // Validate CSRF Token
        if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
            die("Invalid CSRF Token!");
        }

        // Check for empty fields
        if (empty($first_name) || empty($last_name) || empty($email) || empty($role) || empty($password)) {
            echo '<script>alert("All fields are required!");</script>';
            exit();
        }

        // Validate password complexity
        if (!validatePassword($password)) {
            echo '<script>alert("Password must be at least 8 characters long and contain at least one letter and one number.");</script>';
            exit();
        }

        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email;");
            $stmt->execute([":email" => $email]);

            if ($stmt->rowCount() > 0) {
                echo '<script>alert("Email already exists! Please use a different email.");</script>';
                exit();
            }

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (:first_name, :last_name, :email, :password, :role)");
            $stmt->execute([
                ":first_name" => $first_name,
                ":last_name" => $last_name,
                ":email" => $email,
                ":password" => $hashedPassword,
                ":role" => $role
            ]);

            echo '<script>alert("Sign up successful! Redirecting to login..."); window.location.href = "index.php";</script>';
            exit();

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            echo '<script>alert("Something went wrong! Please try again.");</script>';
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link rel="stylesheet" href="./styles/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Sign Up</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="input-group">
                <input type="name" name="first_name">
                <label>First Name</label>
            </div>
            <div class="input-group">
                <input type="name" name="last_name">
                <label>Last Name</label>
            </div>
            <div class="input-group">
                <input type="email" name="email">
                <label>Email</label>
            </div>
            <div class="input-group">
                <input type="password" name="password">
                <label>Password</label>
            </div>

            <div class="input-group">
                <select name="role" id="role" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="procurement">Procurement</option>
                </select>
            </div>

            <div class="forgot-pass">
                <a href="#">Forgot Password?</a>
            </div>
            
            <div class="btn-container">
                <button class="btn" name="submit">Sign Up</button>
            </div>

            <div class="signup-link">
                Already a member? <a href="index.php">Login</a>
            </div>
        </form>
    </div>
</body>
</html>