<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
$name = $_SESSION['name'] ?? null;
?>

<div class="bg-[#0b1f3b] text-white px-6 py-4 flex justify-between items-center shadow">

    <!-- LEFT: BRAND -->
    <div class="text-xl font-bold tracking-wide">
        🐾 PawTrack
    </div>

    <!-- CENTER (optional quick links) -->
    <div class="hidden md:flex gap-6 text-sm">

        <a href="/PawTrack/index.php" class="hover:text-[#4ec5c1]">
            Home
        </a>

        <a href="/PawTrack/cats/list.php" class="hover:text-[#4ec5c1]">
            Cats
        </a>

        <a href="/PawTrack/shelters/list.php" class="hover:text-[#4ec5c1]">
            Shelters
        </a>

        <a href="/PawTrack/donation/list.php" class="hover:text-[#4ec5c1]">
            Donate
        </a>

    </div>

    <!-- RIGHT: USER -->
    <div class="flex items-center gap-4">

        <?php if ($role) { ?>

            <span class="text-sm text-gray-200">
                Hi, <?php echo $name; ?>
            </span>

            <a
                href="/PawTrack/auth/logout.php"
                class="bg-[#3679f7] px-3 py-1 rounded hover:bg-[#4ec5c1] transition"
            >
                Logout
            </a>

        <?php } else { ?>

            <a
                href="/PawTrack/auth/login.php"
                class="bg-[#3679f7] px-3 py-1 rounded hover:bg-[#4ec5c1] transition"
            >
                Login
            </a>

            <a
                href="/PawTrack/auth/register.php"
                class="border border-white px-3 py-1 rounded hover:bg-white hover:text-[#0b1f3b] transition"
            >
                Register
            </a>

        <?php } ?>

    </div>

</div>