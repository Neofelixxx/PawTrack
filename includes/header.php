<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>

    <title>PawTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 min-h-screen">

<!-- NAVBAR -->
<nav class="bg-[#0b1f3b] shadow-lg">

    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        <!-- LEFT SIDE -->
        <div class="flex items-center gap-4">

            <!-- HAMBURGER -->
            <button
                onclick="toggleSidebar()"
                class="text-white text-2xl hover:text-[#4ec5c1]"
            >
                ☰
            </button>

            <!-- LOGO -->
            <a href="/PawTrack/index.php"
               class="text-3xl font-bold text-white tracking-wide">

                🐾 PawTrack

            </a>

        </div>

        <!-- MENU -->
        <div class="flex items-center gap-4">

            <a href="/PawTrack/cats/list.php"
               class="text-white hover:text-[#4ec5c1] transition duration-300">
                Cats
            </a>

            <a href="/PawTrack/intake/map.php"
               class="text-white hover:text-[#4ec5c1] transition duration-300">
                Map
            </a>

            <?php if (isset($_SESSION['user'])) { ?>

                <a href="/PawTrack/dashboard/index.php"
                   class="text-white hover:text-[#4ec5c1] transition duration-300">
                    Dashboard
                </a>

                <a href="/PawTrack/auth/logout.php"
                   class="bg-[#3679f7] hover:bg-[#4ec5c1] text-white px-4 py-2 rounded-lg">
                    Logout
                </a>

            <?php } else { ?>

                <a href="/PawTrack/auth/login.php"
                   class="bg-[#3679f7] hover:bg-[#4ec5c1] text-white px-4 py-2 rounded-lg">
                    Login
                </a>

            <?php } ?>

        </div>

    </div>

</nav>

<!-- CONTENT WRAPPER -->
<div class="flex">

<script>
function toggleSidebar() {

    const sidebar = document.getElementById("sidebar");
    const backdrop = document.getElementById("backdrop");

    sidebar.classList.toggle("-translate-x-full");
    backdrop.classList.toggle("hidden");

}
</script>