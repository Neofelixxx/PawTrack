<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['role'] ?? 'Public';

// Detect if we are running through the virtual host domain or standard localhost subfolder
$base_path = ($_SERVER['HTTP_HOST'] === 'pawtrack.local') ? '/' : '/PawTrack/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PawTrack — Stray Cat Shelter Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body style="background-color: #90D5FF;" class="text-slate-800 min-h-screen flex flex-col">

<?php include(__DIR__ . "/navbar.php"); ?>

<?php include(__DIR__ . "/sidebar.php"); ?>

<div class="flex-1 max-w-7xl w-full mx-auto px-6 py-8">

<script>
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const backdrop = document.getElementById("backdrop");
    
    if (sidebar && backdrop) {
        sidebar.classList.toggle("-translate-x-full");
        backdrop.classList.toggle("hidden");
    } else {
        console.error("Sidebar elements could not be initialized natively in DOM.");
    }
}
</script>