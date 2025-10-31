<?php 

    require_once "./handlers/config.php";
    require_once "./handlers/database_handler.php";

    // Generate CSRF Token if not set
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    // Rate limiting: Prevent brute force attacks
    define('MAX_ATTEMPTS', 5); // Maximum login attempts
    define('LOCKOUT_TIME', 900); // Lockout for 15 minutes (900 seconds)

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"];

        // Validate CSRF Token
        if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
            die("Invalid CSRF Token!");
        }

         // Check rate limit
        if (isset($_SESSION["login_attempts"]) && $_SESSION["login_attempts"] >= MAX_ATTEMPTS) {
            if (time() - $_SESSION["last_attempt_time"] < LOCKOUT_TIME) {
                die("Too many failed attempts. Try again later.");
            } else {
                // Reset attempts after lockout time
                $_SESSION["login_attempts"] = 0;
            }
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("Invalid email format.");
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email;");
            $stmt->execute([
                ":email" => $email
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password"])) {
                // Regenerate session for security
                session_regenerate_id(true);

                // Set session variables
                $_SESSION["user_id"] = htmlspecialchars($user["user_id"]);
                $_SESSION["first_name"] = htmlspecialchars($user["first_name"]);
                $_SESSION["last_name"] = htmlspecialchars($user["last_name"]);
                $_SESSION["email"] = htmlspecialchars($user["email"]);
                $_SESSION["role"] = htmlspecialchars($user["role"]);

                $_SESSION["last_activity"] = time(); // Update last activity timestamp
                $_SESSION["login_attempts"] = 0; // Reset failed attempts

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                 // Log failed login attempts
                error_log("Failed login attempt for email: " . $email);

                $_SESSION["login_attempts"] = ($_SESSION["login_attempts"] ?? 0) + 1;
                $_SESSION["last_attempt_time"] = time();

                echo "<script>alert('Invalid email or password. Please try again.');</script>";
            }
            
        } catch (PDOException $e) {
            error_log("Database error in login: " . $e->getMessage()); // Log error for debugging
            die("Something went wrong. Please try again later.");
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./styles/style.css">
</head>
    <body>

    <div class="login-container">
        <h1>Welcome Back</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <!-- Email -->
            <div class="input-group">
                <input type="email" name="email" id="email" required>
                <label for="email">Email</label>
            </div>

            <!-- Password -->
            <div class="input-group">
                <input type="password" name="password" id="password" required>
                <label for="password">Password</label>
            </div>

            <!-- Forgot password -->
            <div class="forgot-pass">
                <a href="#">Forgot Password?</a>
            </div>

            <!-- Submit -->
            <div class="btn-container">
                <button type="submit" class="btn">Login</button>
            </div>

            <!-- Signup -->
            <div class="signup-link">
                Not a member? <a href="sign_up.php">Sign Up</a>
            </div>
        </form>
    </div>

</body>
</html>
