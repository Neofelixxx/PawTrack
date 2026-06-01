<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? null;
$name = $_SESSION['name'] ?? null;
?>

<!-- STANDALONE RESPONSIVE PLATFORM NAVBAR -->
<div class="bg-white/95 backdrop-blur-md border-b border-sky-100/60 text-slate-700 h-20 px-6 flex justify-between items-center shadow-sm sticky top-0 z-30">
    
    <!-- LEFT SIDE: BRAND LOGO -->
    <a href="/PawTrack/index.php" class="flex items-center gap-3 group">
        <img src="/PawTrack/assets/images/Cat Logo.png" 
             alt="PawTrack" 
             class="w-9 h-9 object-cover rounded-full border border-sky-200 shadow-sm group-hover:scale-105 transition duration-300">
        <span class="text-2xl font-extrabold tracking-tight text-sky-600 group-hover:text-sky-700 transition">
            PawTrack
        </span>
    </a>

    <!-- CENTER NAVIGATION ROUTING LINKS -->
    <div class="hidden md:flex items-center gap-1 font-medium text-slate-600">
        <a href="/PawTrack/index.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition duration-200">Home</a>
        <a href="/PawTrack/cats/list.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition duration-200">Browse Cats</a>
        <a href="/PawTrack/shelters/list.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition duration-200">Shelters</a>
        <a href="/PawTrack/donations/add.php" class="px-4 py-2 rounded-xl hover:text-sky-600 hover:bg-sky-50 transition duration-200">Donate</a>
    </div>

    <!-- RIGHT SIDE: ACCOUNT PROFILE CONTROLS -->
    <div class="flex items-center gap-4">
        <?php if ($role) { ?>
            <span class="text-xs font-bold text-slate-500 bg-sky-50 px-2.5 py-1.5 rounded-lg border border-sky-100/40 truncate max-w-[140px]">
                Hi, <?php echo $name; ?>
            </span>
            <a href="/PawTrack/auth/logout.php" 
               class="bg-slate-800 hover:bg-sky-600 text-white font-semibold px-4 py-2 rounded-xl text-xs transition duration-200 shadow-sm">
                Logout
            </a>
        <?php } else { ?>
            <a href="/PawTrack/auth/login.php" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition px-2 py-1">
                Login
            </a>
            <a href="/PawTrack/auth/register.php" 
               class="bg-sky-500 hover:bg-sky-600 text-white font-semibold px-4 py-2 rounded-xl text-xs transition duration-200 shadow-md">
                Register
            </a>
        <?php } ?>
    </div>
</div>