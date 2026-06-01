<?php
session_start();

function requireLogin($redirect = "/PawTrack/auth/login.php") {
    if (!isset($_SESSION['user'])) {
        header("Location: $redirect");
        exit;
    }
}
?>