<?php

    // Security Headers
    // Security Headers
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:;");
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header_remove("X-Powered-By");

    // error reporting
    error_reporting(E_ALL);
    ini_set("display_errors", 0); // Hide errors from users
    ini_set("log_errors", 1);
    ini_set("error_log", __DIR__ . "/logs/error_log.log"); // Save errors to logs/error_log.log

    // session configuration
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);

    // session cookie configuration
    session_set_cookie_params([
        'lifetime' => 1800,
        'path' => '/',
        'secure' => true,
        'httponly' => true, // prevents javascript from accessing the cookie
        'samesite' => 'Strict' // prevents cross-site request forgery
    ]);

    session_start();

    // Check for session activity timeout (idle time)
    $inactive = 1800; // 30 minutes
    if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > $inactive)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION["last_activity"] = time(); // Update last activity timestamp

    if (isset($_SESSION["user_id"])) {
        if (!isset($_SESSION["last_generation"])) {
            regenerate_session_id_loggedin();
        } else {
            $interval = 1800; // 30 minutes

            if (time() - $_SESSION["last_generation"] >= $interval) {
                regenerate_session_id_loggedin();
            }
        }
    } else {
        if (!isset($_SESSION["last_generation"])) {
            regenerate_session_id();
        } else {
            $interval = 1800; // 30 minutes

            if (time() - $_SESSION["last_generation"] >= $interval) {
                regenerate_session_id();
            }
        }
    }

    function regenerate_session_id_loggedin() {
        session_regenerate_id(true);
        $_SESSION["last_generation"] = time();
    }

    function regenerate_session_id() {
        session_regenerate_id(true);
        $_SESSION["last_generation"] = time();
    }