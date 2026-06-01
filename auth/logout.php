<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Unset all session keys completely
$_SESSION = array();

// 2. Kill the active browser cookie tracking token completely
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Clear the cache memory arrays from the web engine environment
session_destroy();

// 4. Send the user back to the login screen cleanly
header("Location: /PawTrack/auth/login.php");
exit;