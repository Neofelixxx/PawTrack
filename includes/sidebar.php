<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? null;
?>

<!-- BACKDROP (mobile overlay) -->
<div
    id="backdrop"
    class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"
    onclick="toggleSidebar()"
></div>

<!-- SIDEBAR -->
<div
    id="sidebar"
    class="
        fixed top-0 left-0 h-full w-64 bg-white shadow-lg
        transform -translate-x-full
        transition-transform duration-300
        z-50
    "
>

    <div class="p-5 border-b font-bold text-[#0b1f3b]">
        Dashboard
    </div>

    <div class="p-5 space-y-3 text-sm">

        <?php if ($role == "Admin") { ?>

            <a href="/PawTrack/dashboard/index.php" class="block hover:text-[#3679f7]">Overview</a>
            <a href="/PawTrack/cats/list.php" class="block hover:text-[#3679f7]">Manage Cats</a>
            <a href="/PawTrack/shelters/list.php" class="block hover:text-[#3679f7]">Shelters</a>
            <a href="/PawTrack/adoption/list.php" class="block hover:text-[#3679f7]">Adoptions</a>
            <a href="/PawTrack/intake/map.php" class="block hover:text-[#3679f7]">GIS Map</a>
            <a href="/PawTrack/reports/index.php" class="block hover:text-[#3679f7]">Reports</a>

        <?php } elseif ($role == "Staff") { ?>

            <a href="/PawTrack/dashboard/index.php" class="block hover:text-[#3679f7]">Dashboard</a>
            <a href="/PawTrack/cats/list.php" class="block hover:text-[#3679f7]">Cats</a>
            <a href="/PawTrack/cats/add.php" class="block hover:text-[#3679f7]">Add Cat</a>
            <a href="/PawTrack/vaccination/list.php" class="block hover:text-[#3679f7]">Vaccination</a>
            <a href="/PawTrack/adoption/list.php" class="block hover:text-[#3679f7]">Adoptions</a>

        <?php } elseif ($role == "Adopter") { ?>

            <a href="/PawTrack/cats/list.php" class="block hover:text-[#3679f7]">Browse Cats</a>
            <a href="/PawTrack/adoption/list.php" class="block hover:text-[#3679f7]">My Adoptions</a>
            <a href="/PawTrack/donation/list.php" class="block hover:text-[#3679f7]">Donate</a>

        <?php } else { ?>

            <p class="text-gray-500">Please login.</p>

        <?php } ?>

    </div>

</div>