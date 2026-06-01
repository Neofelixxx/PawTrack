<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PawTrack — Stray Cat Shelter Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Importing a beautiful, soft font for an friendly, elite tech look -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

<!-- PREMIUM NAVIGATION BAR -->
<nav class="sticky top-0 bg-white/80 backdrop-blur-md border-b border-gray-100 z-40 transition-all">
    <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
        
        <!-- LEFT SIDE: BRAND LOGO -->
        <div class="flex items-center gap-6">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-orange-500 text-2xl transition p-2 rounded-xl hover:bg-gray-50">
                ☰
            </button>
            <a href="/PawTrack/index.php" class="flex items-center gap-2 group">
                <span class="text-2xl">🐾</span>
                <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-orange-500 to-amber-600 bg-clip-text text-transparent group-hover:opacity-90 transition">
                    PawTrack
                </span>
            </a>
        </div>

        <!-- CENTER NAVIGATION LINKS (Clean & Interactive) -->
        <div class="hidden md:flex items-center gap-1 font-medium text-gray-600">
            <a href="/PawTrack/cats/list.php" class="px-4 py-2 rounded-xl hover:text-orange-500 hover:bg-orange-50 transition duration-200">Browse Cats</a>
            <a href="/PawTrack/shelters/list.php" class="px-4 py-2 rounded-xl hover:text-orange-500 hover:bg-orange-50 transition duration-200">Shelters</a>
            <a href="/PawTrack/intake/map.php" class="px-4 py-2 rounded-xl hover:text-orange-500 hover:bg-orange-50 transition duration-200">GIS Hotspots</a>
            <a href="/PawTrack/donation/add.php" class="px-4 py-2 rounded-xl hover:text-orange-500 hover:bg-orange-50 transition duration-200">Donate</a>
        </div>

        <!-- RIGHT SIDE: USER STATUS ACTIONS -->
        <div class="flex items-center gap-4">
            <?php if (isset($_SESSION['user'])) { ?>
                <a href="/PawTrack/dashboard/index.php" class="text-sm font-semibold text-gray-700 hover:text-orange-500 transition">
                    Dashboard
                </a>
                <a href="/PawTrack/auth/logout.php" class="bg-gray-900 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:shadow transition-all duration-200 text-sm">
                    Logout
                </a>
            <?php } else { ?>
                <a href="/PawTrack/auth/login.php" class="text-sm font-semibold text-gray-600 hover:text-orange-500 transition px-3 py-2">
                    Login
                </a>
                <a href="/PawTrack/auth/register.php" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-sm">
                    Get Started
                </a>
            <?php } ?>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT LAYOUT WRAPPER -->
<div class="flex-1 max-w-7xl w-full mx-auto px-6 py-8">

<script>
function toggleSidebar() {

    const sidebar = document.getElementById("sidebar");
    const backdrop = document.getElementById("backdrop");

    sidebar.classList.toggle("-translate-x-full");
    backdrop.classList.toggle("hidden");

}
</script>