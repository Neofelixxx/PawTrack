<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin($redirect = "/PawTrack/auth/login.php") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: $redirect");
        exit;
    }
}
?>