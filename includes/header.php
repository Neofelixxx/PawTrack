<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PawTrack — Stray Cat Shelter Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

<!-- PASTEL BLUE STICKY NAVIGATION BAR -->
<nav class="sticky top-0 bg-white/90 backdrop-blur-md border-b border-sky-100/60 z-40 transition-all">
    <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
        
        <!-- LEFT SIDE: BRAND LOGO & TITLE -->
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="text-sky-700 hover:text-sky-900 text-2xl transition p-2 rounded-xl hover:bg-sky-50">
                ☰
            </button>
            <a href="/PawTrack/index.php" class="flex items-center gap-3 group">
                <!-- FIXED: Renders the beautiful Cat Logo.png inside the navbar -->
                <img src="/PawTrack/assets/images/Cat Logo.png" 
                     alt="PawTrack Logo" 
                     class="w-9 h-9 object-cover rounded-full border border-sky-200 shadow-sm group-hover:scale-105 transition duration-300">
                <span class="text-2xl font-extrabold tracking-tight text-sky-600 group-hover:text-sky-700 transition">
                    PawTrack
                </span>
            </a>
        </div>

        <!-- CENTER NAVIGATION LINKS -->
        <div class="hidden md:flex items-center gap-1 font-medium text-slate-600">
            <a href="/PawTrack/cats/list.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition duration-200">Browse Cats</a>
            <a href="/PawTrack/shelters/list.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition duration-200">Shelters</a>
            <a href="/PawTrack/intake/map.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition duration-200">GIS Hotspots</a>
            <a href="/PawTrack/donation/add.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition duration-200">Donate</a>
        </div>

        <!-- RIGHT SIDE: USER STATUS ACTIONS -->
        <div class="flex items-center gap-4">
            <?php if (isset($_SESSION['user'])) { ?>
                <a href="/PawTrack/dashboard/index.php" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition">
                    Dashboard
                </a>
                <a href="/PawTrack/auth/logout.php" class="bg-slate-800 hover:bg-sky-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-sm transition-all duration-200 text-sm">
                    Logout
                </a>
            <?php } else { ?>
                <a href="/PawTrack/auth/login.php" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition px-3 py-2">
                    Login
                </a>
                <a href="/PawTrack/auth/register.php" class="bg-sky-500 hover:bg-sky-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-sm">
                    Register
                </a>
            <?php } ?>
        </div>
    </div>
</nav>

<!-- CONTENT WRAPPER -->
<div class="flex-1 max-w-7xl w-full mx-auto px-6 py-8">

<script>
function toggleSidebar() {

    const sidebar = document.getElementById("sidebar");
    const backdrop = document.getElementById("backdrop");

    sidebar.classList.toggle("-translate-x-full");
    backdrop.classList.toggle("hidden");

}
</script>